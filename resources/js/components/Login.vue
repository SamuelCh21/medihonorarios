<script setup>
import { ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['login'])

const email = ref('')
const password = ref('')
const mensaje = ref('')

async function entrar() {
  try {
    await axios.post('/auth/login', { email: email.value, password: password.value })
    emit('login')
  } catch (e) {
    mensaje.value = 'Datos incorrectos'
  }
}
</script>

<template>
  <div class="tarjeta login">
    <h2>MediHonorarios</h2>
    <input v-model="email" placeholder="Correo" />
    <input v-model="password" type="password" placeholder="Contraseña" @keyup.enter="entrar" />
    <button @click="entrar">Entrar</button>
    <p class="mensaje">{{ mensaje }}</p>
  </div>
</template>
