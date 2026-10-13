<script setup>
import LogoutButton from '@/components/LogoutButton.vue'
import { getCurrentUser } from '../services/authService.js'

console.log(getCurrentUser)
</script>

<template>
  <h1>Home page</h1>

  <LogoutButton />
</template>
