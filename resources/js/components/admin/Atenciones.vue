<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const atenciones = ref([])
const especialidades = ref([])
const medicos = ref([]) // solo los médicos de la especialidad escogida
const especialidadId = ref('') // solo sirve para filtrar, no se guarda en la atención
const editandoId = ref(null)
const form = ref({ medico_id: '', fecha_atencion: '', servicio: '', monto_cobrado: '' })
const error = ref('')

async function cargar() {
  const [a, e] = await Promise.all([axios.get('/api/atenciones'), axios.get('/api/especialidades')])
  atenciones.value = a.data
  especialidades.value = e.data
}

// dropdown dependiente: los médicos se cargan según la especialidad
async function cargarMedicos() {
  if (!especialidadId.value) {
    medicos.value = []
    return
  }
  const res = await axios.get('/api/medicos', { params: { especialidad_id: especialidadId.value } })
  medicos.value = res.data
}

// al cambiar la especialidad se borra el médico escogido y se cargan los nuevos
async function cambiarEspecialidad() {
  form.value.medico_id = ''
  await cargarMedicos()
}

function limpiar() {
  editandoId.value = null
  especialidadId.value = ''
  medicos.value = []
  form.value = { medico_id: '', fecha_atencion: '', servicio: '', monto_cobrado: '' }
  error.value = ''
}

async function guardar() {
  try {
    if (editandoId.value) {
      await axios.put('/api/atenciones/' + editandoId.value, form.value)
    } else {
      await axios.post('/api/atenciones', form.value)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

async function editar(a) {
  editandoId.value = a.id
  especialidadId.value = a.medico.especialidad_id
  await cargarMedicos() // primero llenamos los médicos y después escogemos el de la atención
  form.value = {
    medico_id: a.medico_id,
    fecha_atencion: a.fecha_atencion,
    servicio: a.servicio,
    monto_cobrado: a.monto_cobrado,
  }
  error.value = ''
}

async function eliminar(a) {
  if (!confirm('¿Eliminar esta atención?')) return
  error.value = ''
  try {
    await axios.delete('/api/atenciones/' + a.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar atención' : 'Nueva atención' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <select v-model="especialidadId" @change="cambiarEspecialidad">
        <option value="" disabled>1. Escoge la especialidad</option>
        <option v-for="esp in especialidades" :key="esp.id" :value="esp.id">{{ esp.nombre }}</option>
      </select>
      <select v-model="form.medico_id" :disabled="!especialidadId">
        <option value="" disabled>2. Escoge el médico</option>
        <option v-for="m in medicos" :key="m.id" :value="m.id">{{ m.nombre }}</option>
      </select>
      <input v-model="form.fecha_atencion" type="date" />
      <input v-model="form.servicio" placeholder="Servicio (ej: Consulta general)" />
      <input v-model="form.monto_cobrado" type="number" step="0.01" placeholder="Monto cobrado ($)" />
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Atenciones</h3>
    <table>
      <thead>
        <tr><th>Fecha</th><th>Médico</th><th>Especialidad</th><th>Servicio</th><th>Monto</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="a in atenciones" :key="a.id">
          <td>{{ a.fecha_atencion }}</td>
          <td>{{ a.medico.nombre }}</td>
          <td>{{ a.medico.especialidad.nombre }}</td>
          <td>{{ a.servicio }}</td>
          <td>${{ a.monto_cobrado.toFixed(2) }}</td>
          <td>
            <button class="pequeno" @click="editar(a)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(a)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
