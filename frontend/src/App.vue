<script setup>
import { computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import SideBar from './components/SideBar.vue'

const route = useRoute()
const showSidebar = computed(() => route.meta.requiresAuth === true)
</script>

<template>
  <div :class="{ 'ml-[300px]': showSidebar }">
    <SideBar v-if="showSidebar" />
    <RouterView />
  </div>
</template>

<style scoped></style>
