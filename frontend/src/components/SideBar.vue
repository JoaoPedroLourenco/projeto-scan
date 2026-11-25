<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { Box, House, LayoutGrid, ListChecks, LogOut, ScanBarcode, ShoppingBag } from '@lucide/vue'
import { getCurrentUser, logout, ROLE_LABELS } from '../services/authService.js'

defineProps({ mobileOpen: { type: Boolean, default: false } })
const emit = defineEmits(['navigate'])
const route = useRoute()
const router = useRouter()
const currentUser = ref(null)
const isLoadingUser = ref(true)

const userRole = computed(() => ROLE_LABELS[currentUser.value?.role] || '')
const sections = [
  {
    items: [
      { name: 'Início', path: '/home', icon: House },
      { name: 'Catálogo de Produtos', path: '/catalogo', icon: LayoutGrid },
      { name: 'Scanner', path: '/scanner', icon: ScanBarcode },
      { name: 'Sacola', path: '/sacola', icon: ShoppingBag },
      { name: 'Lista de Compras', path: '/lista', icon: ListChecks },
    ],
  },
  {
    title: 'GERÊNCIA',
    adminOnly: true,
    items: [
      { name: 'Gestão de Estoque', path: '/produtos', icon: Box },
      { name: 'Cadastro de Produtos', path: '/produtos/registro', icon: ScanBarcode },
    ],
  },
]
const visibleSections = computed(() =>
  sections.filter((s) => !s.adminOnly || ['admin', 'manager'].includes(currentUser.value?.role)),
)
const isActive = (item) =>
  item.path === '/produtos'
    ? route.path === '/produtos' || route.path.startsWith('/produtos/editar')
    : route.path === item.path

onMounted(async () => {
  try {
    currentUser.value = await getCurrentUser()
  } catch {
    currentUser.value = null
  } finally {
    isLoadingUser.value = false
  }
})

async function signOut() {
  emit('navigate')
  await logout()
  router.push('/login')
}
</script>

<template>
  <div v-if="mobileOpen" class="fixed inset-0 top-16 z-30 bg-slate-950/25 lg:hidden" @click="emit('navigate')"></div>
  <aside
    :class="[
      'fixed bottom-0 left-0 top-16 z-40 flex w-64 flex-col justify-between border-r border-slate-200 bg-white px-3.5 py-4 transition-transform lg:sticky lg:top-16 lg:z-10 lg:h-[calc(100vh-64px)] lg:translate-x-0',
      mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
    ]"
  >
    <div class="space-y-4">
      <section v-for="(section, index) in visibleSections" :key="index" :class="section.title ? 'border-t border-slate-100 pt-3' : ''">
        <p v-if="section.title" class="block px-3.5 pb-2 text-[10px] font-bold tracking-wider text-slate-400">{{ section.title }}</p>
        <nav class="space-y-1">
          <RouterLink
            v-for="item in section.items"
            :key="item.path"
            :to="item.path"
            @click="emit('navigate')"
            :class="[
              'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-medium transition',
              isActive(item) ? 'bg-blue-50 font-semibold text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
            ]"
          >
            <component :is="item.icon" :size="18" :stroke-width="1.75" /><span>{{ item.name }}</span>
          </RouterLink>
        </nav>
      </section>
    </div>

    <div class="mt-4 flex items-center justify-between gap-1.5 border-t border-slate-100 pt-3">
      <RouterLink
        to="/perfil"
        @click="emit('navigate')"
        :class="['group flex min-w-0 flex-1 items-center gap-2.5 rounded-xl p-2 transition', route.path === '/perfil' ? 'bg-blue-50 text-blue-600' : 'text-slate-800 hover:bg-slate-100']"
        title="Ver perfil"
      >
        <span class="flex min-w-0 flex-col">
          <span v-if="isLoadingUser" class="text-xs text-slate-400">Carregando perfil...</span>
          <template v-else>
            <span class="truncate text-xs font-semibold">{{ currentUser?.name || '' }}</span>
            <span class="truncate text-[10px] text-slate-400">{{ userRole }}</span>
          </template>
        </span>
      </RouterLink>
      <button type="button" @click="signOut" class="shrink-0 rounded-xl p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Encerrar Sessão" aria-label="Sair">
        <LogOut :size="16" :stroke-width="1.75" />
      </button>
    </div>
  </aside>
</template>
