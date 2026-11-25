<script setup>
import { computed, onMounted, ref } from 'vue'
import { Check, Minus, Plus, ShoppingBag, Trash2 } from '@lucide/vue'
import PageContainer from './ui/PageContainer.vue'
import { showAllProducts } from '../services/productService.js'
import { formatCurrency } from '../utils/masks.js'
import { addToList, getListItems, importListToCart, removeListItem, toggleListItem, updateListQuantity } from '../services/shoppingService.js'

const items = ref([])
const products = ref([])
const selectedProductId = ref('')
const showForm = ref(false)
const adding = ref(false)
const listNotice = ref('')

const boughtCount = computed(() => items.value.filter((i) => i.is_checked).length)
const progress = computed(() => (items.value.length ? Math.round((boughtCount.value / items.value.length) * 100) : 0))

async function loadProducts() {
  try {
    const res = await showAllProducts()
    const list = res.data ?? res
    products.value = Array.isArray(list) ? list : list?.data ?? []
  } catch (err) {
    console.error('Erro ao carregar produtos:', err)
  }
}

async function loadList() {
  items.value = await getListItems()
}

async function addItem() {
  if (adding.value) return
  const prod = products.value.find((p) => String(p.id) === String(selectedProductId.value))
  if (!prod) return
  adding.value = true
  try {
    items.value = await addToList(prod, 1)
    selectedProductId.value = ''
    showForm.value = false
  } finally {
    adding.value = false
  }
}

async function sendToCart() {
  const enviados = await importListToCart()
  await loadList()
  if (enviados > 0) {
    listNotice.value = `${enviados} ${enviados === 1 ? 'item enviado' : 'itens enviados'} para a sua Sacola.`
  } else {
    listNotice.value = 'Lista importada para a sua Sacola.'
  }
  setTimeout(() => (listNotice.value = ''), 3500)
}

onMounted(() => {
  loadList()
  loadProducts()
  window.addEventListener('list-updated', loadList)
})
</script>

<template>
  <PageContainer>
    <header class="mb-5 flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Lista de compras</h1>
        <p class="mt-1 text-xs text-slate-500">
          {{ items.length }} {{ items.length === 1 ? 'item' : 'itens' }} · {{ boughtCount }} comprados
        </p>
      </div>
      <div class="flex items-center gap-2">
        <button v-if="boughtCount > 0" type="button" @click="sendToCart" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
          <ShoppingBag :size="15" /> Enviar para Sacola
        </button>
        <button type="button" @click="showForm = !showForm" class="rounded-xl bg-blue-600 p-2.5 text-white shadow-sm hover:bg-blue-700" aria-label="Adicionar item">
          <Plus :size="18" />
        </button>
      </div>
    </header>

    <div v-if="listNotice" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-xs text-emerald-800">{{ listNotice }}</div>

    <form v-if="showForm" class="mb-4 flex gap-2" @submit.prevent="addItem">
      <select v-model="selectedProductId" required class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs outline-none focus:border-blue-400">
        <option value="" disabled>Selecione um produto do catálogo</option>
        <option v-for="product in products" :key="product.id" :value="String(product.id)">
          {{ product.name }} ({{ formatCurrency(product.price) }})
        </option>
      </select>
      <button type="submit" :disabled="adding" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
        {{ adding ? 'Adicionando...' : 'Adicionar' }}
      </button>
    </form>

    <section class="mb-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
      <div class="mb-3 flex justify-between text-[10px] font-semibold uppercase tracking-wide text-slate-500">
        <span>Progresso da lista</span><span class="font-bold text-blue-600">{{ progress }}%</span>
      </div>
      <div class="h-1.5 overflow-hidden rounded-full bg-slate-200">
        <div class="h-full rounded-full bg-blue-600 transition-all duration-300" :style="{ width: `${progress}%` }"></div>
      </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <p v-if="!items.length" class="px-5 py-14 text-center text-sm text-slate-500">
        Sua lista está vazia. Use o botão + para adicionar produtos do catálogo.
      </p>
      <article v-for="item in items" :key="item.id" class="flex items-center gap-3 border-b border-slate-100 px-4 py-3.5 last:border-0 sm:px-5">
        <button type="button" :aria-label="item.is_checked ? 'Marcar como pendente' : 'Marcar como comprado'" class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border transition" :class="item.is_checked ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 text-transparent'" @click="toggleListItem(item)">
          <Check :size="13" />
        </button>
        <div class="min-w-0 flex-1">
          <span class="block truncate text-xs font-medium" :class="item.is_checked ? 'text-slate-400 line-through' : 'text-slate-900'">{{ item.product?.name || 'Produto' }}</span>
          <span class="mt-0.5 block text-[10px] text-slate-400">{{ item.product?.category?.name || 'Geral' }}</span>
        </div>
        <div class="flex items-center rounded-lg border border-slate-200">
          <button type="button" class="p-2 text-slate-500 hover:text-blue-600" aria-label="Diminuir quantidade" @click="updateListQuantity(item, item.quantity - 1)"><Minus :size="12" /></button>
          <span class="min-w-7 text-center text-[11px] font-medium">{{ item.quantity }}</span>
          <button type="button" class="p-2 text-slate-500 hover:text-blue-600" aria-label="Aumentar quantidade" @click="updateListQuantity(item, item.quantity + 1)"><Plus :size="12" /></button>
        </div>
        <button type="button" class="p-2 text-slate-400 hover:text-rose-600" aria-label="Remover item" @click="removeListItem(item.id)"><Trash2 :size="14" /></button>
      </article>
    </section>
  </PageContainer>
</template>
