<script setup>
import { logout } from '../services/authService.js'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const router = useRouter()

const loading = ref(false)

async function logoutUser() {
  loading.value = true

  try {
    await logout()

    router.push('/')
  } catch (error) {
    console.error(error.response?.data)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <button type="button" @click="logoutUser">
    {{ loading ? 'Saindo...' : 'Sair' }}
  </button>
</template>
