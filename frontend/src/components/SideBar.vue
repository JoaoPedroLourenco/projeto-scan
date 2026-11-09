<script setup>
import { RouterLink } from 'vue-router'
import LogoutButton from './LogoutButton.vue'
</script>

<template>
  <aside class="fixed left-0 top-0 w-[250px] h-dvh flex flex-col items-center justify-center">
    <RouterLink to="/produtos">Lista de produtos</RouterLink>
    <RouterLink to="/produtos/registro">Cadastro de produtos</RouterLink>
    <LogoutButton />
  </aside>
</template>
