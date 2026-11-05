<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Login from './components/Login.vue'
import Usuarios from './components/admin/Usuarios.vue'
import Medicos from './components/admin/Medicos.vue'
import Especialidades from './components/admin/Especialidades.vue'
import Reglas from './components/admin/Reglas.vue'
import Recursos from './components/admin/Recursos.vue'
import Atenciones from './components/admin/Atenciones.vue'
import UsosRecurso from './components/admin/UsosRecurso.vue'

const logueado = ref(false)
const role = ref('')
const nombre = ref('')
const cargando = ref(true)

// pestañas del panel del administrador
const pestanas = [
  { id: 'usuarios', texto: 'Usuarios', componente: Usuarios },
  { id: 'medicos', texto: 'Médicos', componente: Medicos },
  { id: 'especialidades', texto: 'Especialidades', componente: Especialidades },
  { id: 'reglas', texto: 'Reglas de honorarios', componente: Reglas },
  { id: 'recursos', texto: 'Recursos', componente: Recursos },
  { id: 'atenciones', texto: 'Atenciones', componente: Atenciones },
  { id: 'usos', texto: 'Usos de recurso', componente: UsosRecurso },
]
const pestana = ref(pestanas[0])

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
  pestana.value = pestanas[0]
}

onMounted(revisarSesion)
</script>

<template>
  <p v-if="cargando">Cargando...</p>
  <Login v-else-if="!logueado" @login="revisarSesion" />
  <div v-else class="contenedor">
    <div class="tarjeta encabezado">
      <h3>MediHonorarios - {{ nombre }} ({{ role }})</h3>
      <button class="rojo" @click="salir">Salir</button>
    </div>

    <!-- panel del administrador -->
    <template v-if="role === 'admin'">
      <div class="menu">
        <button v-for="p in pestanas" :key="p.id" :class="{ activo: pestana.id === p.id }" @click="pestana = p">
          {{ p.texto }}
        </button>
      </div>
      <component :is="pestana.componente" :key="pestana.id" />
    </template>

    <!-- gerencia y médico: sus pantallas vienen con el core -->
    <div v-else class="tarjeta">
      <p>Tus pantallas estarán disponibles pronto.</p>
    </div>
  </div>
</template>
