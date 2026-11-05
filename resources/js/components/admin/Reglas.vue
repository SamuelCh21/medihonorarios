<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const reglas = ref([])
const editandoId = ref(null)
const form = ref({ umbral: '', porcentaje: '' })
const error = ref('')

async function cargar() {
  const res = await axios.get('/api/reglas-honorarios')
  reglas.value = res.data
}

function limpiar() {
  editandoId.value = null
  form.value = { umbral: '', porcentaje: '' }
  error.value = ''
}

async function guardar() {
  try {
    if (editandoId.value) {
      await axios.put('/api/reglas-honorarios/' + editandoId.value, form.value)
    } else {
      await axios.post('/api/reglas-honorarios', form.value)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

function editar(r) {
  editandoId.value = r.id
  form.value = { umbral: r.umbral, porcentaje: r.porcentaje }
  error.value = ''
}

async function eliminar(r) {
  if (!confirm('¿Eliminar la regla de $' + r.umbral + '?')) return
  try {
    await axios.delete('/api/reglas-honorarios/' + r.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar regla' : 'Nueva regla' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <input v-model="form.umbral" type="number" step="0.01" placeholder="Umbral ($)" />
      <input v-model="form.porcentaje" type="number" step="0.01" placeholder="Porcentaje del médico (%)" />
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Reglas de honorarios</h3>
    <p>El porcentaje es lo que se lleva el médico. Siempre debe existir una regla con umbral $0.</p>
    <table>
      <thead>
        <tr><th>Umbral</th><th>Porcentaje del médico</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="r in reglas" :key="r.id">
          <td>${{ r.umbral.toFixed(2) }}</td>
          <td>{{ r.porcentaje }}%</td>
          <td>
            <button class="pequeno" @click="editar(r)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(r)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
