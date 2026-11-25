<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Check, LogOut, Plus, X } from '@lucide/vue'
import BaseButton from './ui/BaseButton.vue'
import PageContainer from './ui/PageContainer.vue'
import { getAllergens, getCurrentUser, logout, ROLE_LABELS, syncUserAllergens } from '../services/authService.js'

const router = useRouter()
const profile = ref(null)
const loading = ref(true)
const errorMessage = ref('')
const restrictions = ref([])
const catalogAllergens = ref([])
const restrictionDraft = ref('')
const addingRestriction = ref(false)

const roleLabel = computed(() => ROLE_LABELS[profile.value?.role] || '')

watch(restrictions, (lista) => {
  localStorage.setItem('beepo-profile-restrictions', JSON.stringify(lista))
}, { deep: true })

async function loadProfile() {
  loading.value = true
  errorMessage.value = ''
  try {
    profile.value = await getCurrentUser()
    catalogAllergens.value = await getAllergens().catch(() => [])
    if (!restrictions.value.length && Array.isArray(profile.value?.allergens)) {
      restrictions.value = profile.value.allergens.map((a) => a.name)
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Não foi possível carregar os dados da conta.'
  } finally {
    loading.value = false
  }
}

async function salvarAlergenosNaApi() {
  if (!catalogAllergens.value.length) return
  const ids = []
  for (const nome of restrictions.value) {
    const achado = catalogAllergens.value.find((a) => a.name?.toLowerCase() === nome.toLowerCase())
    if (achado) ids.push(achado.id)
  }
  try {
    await syncUserAllergens(ids)
  } catch {
    /* mantém salvo localmente */
  }
}

async function addRestriction() {
  const texto = restrictionDraft.value.trim()
  if (!texto) return
  const jaExiste = restrictions.value.some((item) => item.toLowerCase() === texto.toLowerCase())
  if (jaExiste) return

  restrictions.value.push(texto)
  restrictionDraft.value = ''
  addingRestriction.value = false
  await salvarAlergenosNaApi()
}

async function removeRestriction(nome) {
  restrictions.value = restrictions.value.filter((item) => item !== nome)
  await salvarAlergenosNaApi()
}

async function signOut() {
  await logout()
  router.push('/login')
}

onMounted(() => {
  try {
    restrictions.value = JSON.parse(localStorage.getItem('beepo-profile-restrictions') || '[]')
  } catch {
    restrictions.value = []
  }
  loadProfile()
})
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Perfil</h1>
      <p class="mt-1 text-xs text-slate-500">Confira seus dados e gerencie suas restrições alimentares.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
      {{ errorMessage }}
      <button type="button" @click="loadProfile" class="font-semibold underline">Tentar novamente</button>
    </p>
    <p v-else-if="loading" class="mb-4 text-sm text-slate-500">Carregando...</p>

    <section v-else-if="profile" class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:px-5">
      <div class="flex flex-wrap items-center gap-2">
        <h2 class="truncate text-sm font-semibold text-slate-900">{{ profile.name }}</h2>
        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] text-slate-600">{{ roleLabel }}</span>
      </div>
      <p class="mt-1 truncate text-[11px] text-slate-500">{{ profile.email }}</p>
    </section>

    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
      <header class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-xs font-semibold text-slate-900">Restrições Alimentares &amp; Alérgenos</h2>
        <span class="text-[10px] text-slate-400">Alertas ativos no scanner</span>
      </header>
      <p class="max-w-3xl text-[11px] leading-relaxed text-slate-500">
        Ao escanear um código de barras contendo derivados ou traços destes alérgenos, um aviso prioritário será acionado no terminal e na sacola de compras.
      </p>
      <div class="mt-5 flex flex-wrap items-center gap-2">
        <span v-for="item in restrictions" :key="item" class="inline-flex min-h-8 items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 text-[11px] font-medium text-rose-700">
          <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>{{ item }}
          <button type="button" @click="removeRestriction(item)" :aria-label="`Remover ${item}`"><X :size="12" /></button>
        </span>
        <template v-if="addingRestriction">
          <input v-model="restrictionDraft" autofocus maxlength="40" list="allergens-list" placeholder="Ex: Soja, Açúcar..." class="h-8 w-48 rounded-full border border-blue-500 bg-white px-3 text-[11px] text-slate-700 outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-blue-100" @keydown.enter.prevent="addRestriction" />
          <datalist id="allergens-list"><option v-for="alg in catalogAllergens" :key="alg.id" :value="alg.name" /></datalist>
          <BaseButton @click="addRestriction" class="h-6 w-6 shrink-0 rounded-md p-0" aria-label="Confirmar restrição"><Check :size="14" :stroke-width="2.25" /></BaseButton>
          <button type="button" @click="addingRestriction = false; restrictionDraft = ''" class="flex h-6 w-6 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Cancelar inclusão"><X :size="14" /></button>
        </template>
        <BaseButton v-else variant="outline" @click="addingRestriction = true" class="min-h-8 rounded-full border-blue-200 px-3 text-[11px] font-medium">
          <Plus :size="13" /> Adicionar Restrição
        </BaseButton>
      </div>
    </section>

    <footer class="mt-10 flex justify-end border-t border-slate-100 px-1 pt-6">
      <button type="button" @click="signOut" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-rose-200 px-4 py-2.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50">
        <LogOut :size="14" /> Encerrar Sessão
      </button>
    </footer>
  </PageContainer>
</template>
