<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const especialidades = ref([])
const editandoId = ref(null)
const form = ref({ nombre: '' })
const error = ref('')

async function cargar() {
  const res = await axios.get('/api/especialidades')
  especialidades.value = res.data
}

function limpiar() {
  editandoId.value = null
  form.value = { nombre: '' }
  error.value = ''
}

async function guardar() {
  try {
    if (editandoId.value) {
      await axios.put('/api/especialidades/' + editandoId.value, form.value)
    } else {
      await axios.post('/api/especialidades', form.value)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

function editar(esp) {
  editandoId.value = esp.id
  form.value = { nombre: esp.nombre }
  error.value = ''
}

async function eliminar(esp) {
  if (!confirm('¿Eliminar la especialidad ' + esp.nombre + '?')) return
  error.value = ''
  try {
    await axios.delete('/api/especialidades/' + esp.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar especialidad' : 'Nueva especialidad' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <input v-model="form.nombre" placeholder="Nombre de la especialidad" />
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Especialidades</h3>
    <table>
      <thead>
        <tr><th>Nombre</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="esp in especialidades" :key="esp.id">
          <td>{{ esp.nombre }}</td>
          <td>
            <button class="pequeno" @click="editar(esp)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(esp)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
