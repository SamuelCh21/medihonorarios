<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Login from './components/Login.vue'

const logueado = ref(false)
const role = ref('')
const nombre = ref('')
const cargando = ref(true)

async function revisarSesion() {
  const res = await axios.get('/auth/me')
  logueado.value = res.data.logueado
  role.value = res.data.role
  nombre.value = res.data.nombre
  cargando.value = false
}

async function salir() {
  await axios.post('/auth/logout')
  logueado.value = false
  role.value = ''
  nombre.value = ''
}

onMounted(revisarSesion)
</script>

<template>
  <p v-if="cargando">Cargando...</p>
  <Login v-else-if="!logueado" @login="revisarSesion" />
  <div v-else class="contenedor">
    <div class="tarjeta encabezado">
      <h3>Hola, {{ nombre }} ({{ role }})</h3>
      <button class="rojo" @click="salir">Salir</button>
    </div>
  </div>
</template>
