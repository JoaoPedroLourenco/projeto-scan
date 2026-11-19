<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { House, LayoutGrid, ScanBarcode, ShoppingBag, ListChecks, BarChart2, Box, LogOut } from '@lucide/vue'
import { getCurrentUser, logout } from '../services/authService.js'

defineProps({ mobileOpen: { type: Boolean, default: false } })
const emit = defineEmits(['navigate'])
const route = useRoute()
const router = useRouter()
const currentUser = ref(null)
const isLoadingUser = ref(true)
const userName = computed(() => currentUser.value?.name || '')
const userRole = computed(
  () =>
    ({ cliente: 'Cliente', admin: 'Administrador' })[
      currentUser.value?.role
    ] || '',
)
const sections = [
  {
    items: [
      { name: 'Início', path: '/home', icon: House },
      { name: 'Catálogo de Produtos', path: '/catalogo', icon: LayoutGrid },
      { name: 'Scanner', path: '/scanner', icon: ScanBarcode },
      { name: 'Sacola', path: '/sacola', icon: ShoppingBag, unavailable: true },
      { name: 'Lista de Compras', path: '/lista', icon: ListChecks, unavailable: true },
    ],
  },
  {
    title: 'GERÊNCIA',
    items: [
      {
        name: 'Painel do Gerente',
        path: '/gerencia/dashboard',
        icon: BarChart2,
        unavailable: true,
      },
      { name: 'Gestão de Estoque', path: '/produtos', icon: Box },
      { name: 'Cadastro de Produtos', path: '/produtos/registro', icon: ScanBarcode },
    ],
  },
]
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
  <div
    v-if="mobileOpen"
    class="fixed inset-0 top-16 z-30 bg-slate-950/25 lg:hidden"
    @click="emit('navigate')"
  ></div>
  <aside
    :class="[
      [
        'fixed bottom-0 left-0 top-16 z-40 flex w-64 flex-col justify-between',
        'border-r border-slate-200 bg-white px-3.5 py-4 transition-transform',
        'lg:sticky lg:top-16 lg:z-10 lg:h-[calc(100vh-64px)] lg:translate-x-0',
      ],
      mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
    ]"
  >
    <div class="space-y-4">
      <section
        v-for="(section, index) in sections"
        :key="index"
        :class="section.title ? 'border-t border-slate-100 pt-3' : ''"
      >
        <p
          v-if="section.title"
          class="block px-3.5 pb-2 text-[10px] font-bold tracking-wider text-slate-400"
        >
          {{ section.title }}
        </p>
        <nav class="space-y-1">
          <template v-for="item in section.items" :key="item.path">
            <button
              v-if="item.unavailable"
              type="button"
              class="flex w-full cursor-default items-center gap-3 rounded-xl px-3.5 py-2.5 text-left text-xs font-medium text-slate-600"
            >
              <component :is="item.icon" :size="18" :stroke-width="1.75" /><span>{{
                item.name
              }}</span>
            </button>
            <RouterLink
              v-else
              :to="item.path"
              @click="emit('navigate')"
              :class="[
                'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-medium transition',
                isActive(item)
                  ? 'bg-blue-50 font-semibold text-blue-600'
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
              ]"
              ><component :is="item.icon" :size="18" :stroke-width="1.75" /><span>{{
                item.name
              }}</span></RouterLink
            >
          </template>
        </nav>
      </section>
    </div>

    <div class="mt-4 flex items-center justify-between gap-1.5 border-t border-slate-100 pt-3">
      <RouterLink
        to="/perfil"
        @click="emit('navigate')"
        :class="[
          'group flex min-w-0 flex-1 cursor-pointer items-center gap-2.5 rounded-xl p-2 transition',
          route.path === '/perfil' || route.path.startsWith('/perfil/')
            ? 'bg-blue-50 text-blue-600'
            : 'text-slate-800 hover:bg-slate-100',
        ]"
        title="Ver perfil"
      >
        <span class="flex min-w-0 flex-col"
          ><span v-if="isLoadingUser" class="flex items-center gap-2 text-xs text-slate-400">
            <span class="h-3 w-3 animate-spin rounded-full border-2 border-slate-200 border-t-blue-600"></span>
            Carregando perfil...
          </span><span v-else class="truncate text-xs font-semibold">{{ userName }}</span
          ><span class="truncate text-[10px] text-slate-400">{{
            isLoadingUser ? '' : userRole
          }}</span></span
        >
      </RouterLink>
      <button
        type="button"
        @click="signOut"
        class="shrink-0 rounded-xl p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
        title="Encerrar Sessão"
        aria-label="Sair"
      >
        <LogOut :size="16" :stroke-width="1.75" />
      </button>
    </div>
  </aside>
</template>
