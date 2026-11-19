<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { Menu, Search, Bell } from '@lucide/vue'

const route = useRoute()
const router = useRouter()
const search = ref(String(route.query.busca || ''))
const emit = defineEmits(['toggle-menu'])

watch(
  () => route.query.busca,
  (value) => {
    search.value = String(value || '')
  },
)

function submitSearch() {
  router.push({
    path: '/catalogo',
    query: search.value.trim() ? { busca: search.value.trim() } : {},
  })
}
</script>

<template>
  <header class="sticky top-0 z-30 h-16 border-b border-slate-200 bg-white">
    <div class="flex h-full items-center justify-between gap-3 px-4 sm:px-6">
      <div class="flex shrink-0 items-center gap-3">
        <button
          type="button"
          @click="emit('toggle-menu')"
          class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100"
          aria-label="Abrir ou fechar menu"
        >
          <Menu :size="19" />
        </button>
        <RouterLink to="/catalogo" class="flex items-center"
          ><img src="@/assets/logo.svg" alt="beepo." class="h-6 w-auto"
        /></RouterLink>
      </div>
      <form @submit.prevent="submitSearch" class="relative w-full max-w-md">
        <Search
          :size="16"
          class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
        />
        <input
          v-model="search"
          type="search"
          placeholder="Buscar produto..."
          class="
            w-full rounded-lg border border-slate-200 bg-slate-100 py-2 pl-9 pr-3 text-sm
            text-slate-700 outline-none transition placeholder:text-slate-400
            focus:border-blue-300 focus:bg-white focus:ring-2 focus:ring-blue-100
          "
        />
      </form>
      <button
        type="button"
        class="shrink-0 rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
        aria-label="Notificações"
      >
        <Bell :size="18" />
      </button>
    </div>
  </header>
</template>
