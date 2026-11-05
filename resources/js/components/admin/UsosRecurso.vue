<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const usos = ref([])
const medicos = ref([]) // solo externos, a los de planta no se les cobra
const recursos = ref([])
const editandoId = ref(null)
const form = ref({ medico_id: '', recurso_id: '', fecha_uso: '', horas: '' })
const error = ref('')

async function cargar() {
  const [u, m, r] = await Promise.all([
    axios.get('/api/usos-recurso'),
    axios.get('/api/medicos', { params: { tipo_vinculacion: 'externo' } }),
    axios.get('/api/recursos'),
  ])
  usos.value = u.data
  medicos.value = m.data
  recursos.value = r.data
}

function limpiar() {
  editandoId.value = null
  form.value = { medico_id: '', recurso_id: '', fecha_uso: '', horas: '' }
  error.value = ''
}

async function guardar() {
  try {
    if (editandoId.value) {
      await axios.put('/api/usos-recurso/' + editandoId.value, form.value)
    } else {
      await axios.post('/api/usos-recurso', form.value)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

function editar(u) {
  editandoId.value = u.id
  form.value = { medico_id: u.medico_id, recurso_id: u.recurso_id, fecha_uso: u.fecha_uso, horas: u.horas }
  error.value = ''
}

async function eliminar(u) {
  if (!confirm('¿Eliminar este uso de recurso?')) return
  try {
    await axios.delete('/api/usos-recurso/' + u.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar uso de recurso' : 'Nuevo uso de recurso' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <select v-model="form.medico_id">
        <option value="" disabled>Escoge el médico (externo)</option>
        <option v-for="m in medicos" :key="m.id" :value="m.id">{{ m.nombre }}</option>
      </select>
      <select v-model="form.recurso_id">
        <option value="" disabled>Escoge el recurso</option>
        <option v-for="r in recursos" :key="r.id" :value="r.id">{{ r.nombre }} (${{ r.tarifa_hora.toFixed(2) }}/h)</option>
      </select>
      <input v-model="form.fecha_uso" type="date" />
      <input v-model="form.horas" type="number" step="0.25" placeholder="Horas de uso" />
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Usos de recurso</h3>
    <table>
      <thead>
        <tr><th>Fecha</th><th>Médico</th><th>Recurso</th><th>Horas</th><th>Costo</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="u in usos" :key="u.id">
          <td>{{ u.fecha_uso }}</td>
          <td>{{ u.medico.nombre }}</td>
          <td>{{ u.recurso.nombre }}</td>
          <td>{{ u.horas }}</td>
          <td>${{ (u.horas * u.recurso.tarifa_hora).toFixed(2) }}</td>
          <td>
            <button class="pequeno" @click="editar(u)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(u)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
