<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const recursos = ref([])
const editandoId = ref(null)
const form = ref({ nombre: '', tarifa_hora: '' })
const error = ref('')

async function cargar() {
  const res = await axios.get('/api/recursos')
  recursos.value = res.data
}

function limpiar() {
  editandoId.value = null
  form.value = { nombre: '', tarifa_hora: '' }
  error.value = ''
}

async function guardar() {
  try {
    if (editandoId.value) {
      await axios.put('/api/recursos/' + editandoId.value, form.value)
    } else {
      await axios.post('/api/recursos', form.value)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

function editar(r) {
  editandoId.value = r.id
  form.value = { nombre: r.nombre, tarifa_hora: r.tarifa_hora }
  error.value = ''
}

async function eliminar(r) {
  if (!confirm('¿Eliminar el recurso ' + r.nombre + '?')) return
  error.value = ''
  try {
    await axios.delete('/api/recursos/' + r.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar recurso' : 'Nuevo recurso' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <input v-model="form.nombre" placeholder="Nombre (ej: Quirófano)" />
      <input v-model="form.tarifa_hora" type="number" step="0.01" placeholder="Tarifa por hora ($)" />
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Recursos de la clínica</h3>
    <table>
      <thead>
        <tr><th>Nombre</th><th>Tarifa por hora</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="r in recursos" :key="r.id">
          <td>{{ r.nombre }}</td>
          <td>${{ r.tarifa_hora.toFixed(2) }}</td>
          <td>
            <button class="pequeno" @click="editar(r)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(r)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
