<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { mensajeError } from '../../errores'

const usuarios = ref([])
const editandoId = ref(null)
const form = ref({ name: '', email: '', password: '', role: 'medico' })
const error = ref('')

async function cargar() {
  const res = await axios.get('/api/usuarios')
  usuarios.value = res.data
}

function limpiar() {
  editandoId.value = null
  form.value = { name: '', email: '', password: '', role: 'medico' }
  error.value = ''
}

async function guardar() {
  try {
    if (editandoId.value) {
      await axios.put('/api/usuarios/' + editandoId.value, form.value)
    } else {
      await axios.post('/api/usuarios', form.value)
    }
    limpiar()
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

function editar(u) {
  editandoId.value = u.id
  // la contraseña se deja vacía: si no se escribe nada, se mantiene la anterior
  form.value = { name: u.name, email: u.email, password: '', role: u.role }
  error.value = ''
}

async function eliminar(u) {
  if (!confirm('¿Eliminar a ' + u.name + '?')) return
  try {
    await axios.delete('/api/usuarios/' + u.id)
    cargar()
  } catch (e) {
    error.value = mensajeError(e)
  }
}

onMounted(cargar)
</script>

<template>
  <div class="tarjeta">
    <h3>{{ editandoId ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
    <form class="formulario" @submit.prevent="guardar">
      <input v-model="form.name" placeholder="Nombre" />
      <input v-model="form.email" placeholder="Correo" />
      <input v-model="form.password" type="password" :placeholder="editandoId ? 'Contraseña (vacía = no cambiar)' : 'Contraseña'" />
      <select v-model="form.role">
        <option value="admin">Administrador</option>
        <option value="gerencia">Gerencia</option>
        <option value="medico">Médico</option>
      </select>
      <button type="submit">{{ editandoId ? 'Actualizar' : 'Guardar' }}</button>
      <button v-if="editandoId" type="button" class="secundario" @click="limpiar">Cancelar</button>
    </form>
    <p class="error">{{ error }}</p>
  </div>

  <div class="tarjeta">
    <h3>Usuarios</h3>
    <table>
      <thead>
        <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="u in usuarios" :key="u.id">
          <td>{{ u.name }}</td>
          <td>{{ u.email }}</td>
          <td>{{ u.role }}</td>
          <td>
            <button class="pequeno" @click="editar(u)">Editar</button>
            <button class="pequeno rojo" @click="eliminar(u)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
