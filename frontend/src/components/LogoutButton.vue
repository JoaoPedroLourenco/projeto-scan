<script setup>
import { logout } from '../services/authService.js'
import { useRouter } from 'vue-router'

const router = useRouter()

async function logoutUser() {
  await logout()

  router.push('/register')
}
</script>

<template>
  <button @click="logoutUser">Sair</button>
</template>
