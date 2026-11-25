<script setup>
import { computed, ref, watch } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import AppHeader from './components/AppHeader.vue'
import SideBar from './components/SideBar.vue'

const route = useRoute()
const showNavigation = computed(() => route.meta.requiresAuth === true)
const mobileMenuOpen = ref(false)
const sidebarOpen = ref(true)

watch(
  () => route.fullPath,
  () => {
    mobileMenuOpen.value = false
  },
)

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
  mobileMenuOpen.value = sidebarOpen.value
}
</script>

<template>
  <div class="min-h-screen bg-white text-slate-800 antialiased">
    <AppHeader
      v-if="showNavigation"
      @toggle-menu="toggleSidebar"
    />
    <div class="flex min-h-[calc(100vh-64px)]">
      <SideBar
        v-if="showNavigation && sidebarOpen"
        :mobile-open="mobileMenuOpen"
        @navigate="mobileMenuOpen = false"
      />
      <main class="min-w-0 flex-1"><RouterView /></main>
    </div>
  </div>
</template>
