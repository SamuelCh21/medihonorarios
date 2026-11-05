<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const medicos = ref([])
const especialidades = ref([])
const usuariosMedico = ref([]) // solo los usuarios con rol médico
const editandoId = ref(null)
const form = ref({ nombre: '', cedula: '', especialidad_id: '', tipo_vinculacion: 'externo', user_id: '' })
const error = ref('')

async function cargar() {
  const [m, e, u] = await Promise.all([
    axios.get('/api/medicos'),
    axios.get('/api/especialidades'),
    axios.get('/api/usuarios'),
  ])
  medicos.value = m.data
  especialidades.value = e.data
  usuariosMedico.value = u.data.filter((x) => x.role === 'medico')
}

function limpiar() {
  editandoId.value = null
  form.value = { nombre: '', cedula: '', especialidad_id: '', tipo_vinculacion: 'externo', user_id: '' }
  error.value = ''
}

async function guardar() {
  // si no se escogió usuario mandamos null
  const datos = { ...form.value, user_id: form.value.user_id || null }
  try {
    if (editandoId.value) {
      await axios.put('/api/medicos/' + editandoId.value, datos)
    } else {
      await axios.post('/api/medicos', datos)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

function editar(m) {
  editandoId.value = m.id
  form.value = {
    nombre: m.nombre,
    cedula: m.cedula,
    especialidad_id: m.especialidad_id,
    tipo_vinculacion: m.tipo_vinculacion,
    user_id: m.user_id || '',
  }
  error.value = ''
}

async function eliminar(m) {
  if (!confirm('¿Eliminar a ' + m.nombre + '?')) return
  try {
    await axios.delete('/api/medicos/' + m.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar médico' : 'Nuevo médico' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <input v-model="form.nombre" placeholder="Nombre" />
      <input v-model="form.cedula" placeholder="Cédula (10 dígitos)" maxlength="10" />
      <select v-model="form.especialidad_id">
        <option value="" disabled>Escoge una especialidad</option>
        <option v-for="esp in especialidades" :key="esp.id" :value="esp.id">{{ esp.nombre }}</option>
      </select>
      <select v-model="form.tipo_vinculacion">
        <option value="externo">Externo</option>
        <option value="planta">Planta</option>
      </select>
      <select v-model="form.user_id">
        <option value="">Sin usuario</option>
        <option v-for="u in usuariosMedico" :key="u.id" :value="u.id">{{ u.name }} ({{ u.email }})</option>
      </select>
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Médicos</h3>
    <table>
      <thead>
        <tr><th>Nombre</th><th>Cédula</th><th>Especialidad</th><th>Vinculación</th><th>Usuario</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="m in medicos" :key="m.id">
          <td>{{ m.nombre }}</td>
          <td>{{ m.cedula }}</td>
          <td>{{ m.especialidad.nombre }}</td>
          <td><span class="etiqueta">{{ m.tipo_vinculacion }}</span></td>
          <td>{{ m.user ? m.user.email : '-' }}</td>
          <td>
            <button class="pequeno" @click="editar(m)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(m)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
