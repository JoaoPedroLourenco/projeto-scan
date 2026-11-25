<script setup>
import { computed, onMounted, ref } from 'vue'
import { ArrowRight, Minus, Plus, Trash2 } from '@lucide/vue'
import PageContainer from './ui/PageContainer.vue'
import { formatCurrency } from '../utils/masks.js'
import { checkoutCart, getCartItems, removeCartItem, updateCartQuantity } from '../services/shoppingService.js'

const items = ref([])
const loading = ref(false)
const checkoutSuccess = ref(false)
const error = ref('')
const total = computed(() =>
  items.value.reduce((acc, i) => acc + Number(i.unit_price || i.product?.price || 0) * (i.quantity || 1), 0),
)

async function loadCart() {
  items.value = await getCartItems()
}

async function changeQty(item, nextQty) {
  error.value = ''
  items.value = await updateCartQuantity(item.id, nextQty)
}

async function removeItem(item) {
  error.value = ''
  items.value = await removeCartItem(item.id)
}

async function finalizeCheckout() {
  if (!items.value.length || loading.value) return
  loading.value = true
  error.value = ''
  try {
    await checkoutCart()
    items.value = []
    checkoutSuccess.value = true
  } catch (err) {
    error.value = err.response?.data?.msg || err.response?.data?.message || 'Não foi possível finalizar a compra.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCart()
  window.addEventListener('cart-updated', loadCart)
})
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Sacola</h1>
      <p class="mt-1 text-xs text-slate-500">
        {{ items.length }} {{ items.length === 1 ? 'item' : 'itens' }} · Total {{ formatCurrency(total) }}
      </p>
    </header>

    <div v-if="checkoutSuccess" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800">
      <strong>Compra finalizada com sucesso!</strong> Seu pedido foi concluído.
    </div>
    <p v-if="error" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">{{ error }}</p>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <p v-if="!items.length && !checkoutSuccess" class="px-5 py-16 text-center text-sm text-slate-500">
        Sua sacola está vazia. Adicione produtos pelo catálogo ou pelo scanner.
      </p>
      <article v-for="item in items" :key="item.id" class="flex items-center gap-3 border-b border-slate-100 px-4 py-4 last:border-0 sm:px-5">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
          <img v-if="item.product?.image_url" :src="item.product.image_url" :alt="item.product?.name" class="h-full w-full object-contain p-1" />
        </span>
        <div class="min-w-0 flex-1">
          <strong class="block truncate text-xs font-semibold text-slate-900">{{ item.product?.name || 'Produto' }}</strong>
          <span class="mt-0.5 block text-[10px] text-slate-400">{{ item.product?.category?.name || 'Geral' }}</span>
          <strong class="mt-1 block text-xs text-slate-900">{{ formatCurrency(item.unit_price || item.product?.price) }}</strong>
        </div>
        <div class="flex items-center rounded-lg border border-slate-200">
          <button class="p-2 text-slate-500 hover:text-blue-600" aria-label="Diminuir quantidade" @click="changeQty(item, item.quantity - 1)"><Minus :size="13" /></button>
          <span class="min-w-7 text-center text-xs font-medium">{{ item.quantity }}</span>
          <button class="p-2 text-slate-500 hover:text-blue-600" aria-label="Aumentar quantidade" @click="changeQty(item, item.quantity + 1)"><Plus :size="13" /></button>
        </div>
        <button class="p-2 text-slate-400 hover:text-rose-600" aria-label="Remover produto" @click="removeItem(item)"><Trash2 :size="15" /></button>
      </article>
    </section>

    <section v-if="items.length" class="mt-6 space-y-3">
      <div class="flex justify-between text-xs text-slate-500"><span>Subtotal</span><span>{{ formatCurrency(total) }}</span></div>
      <div class="flex items-center justify-between border-t border-slate-100 pt-3">
        <strong class="text-sm text-slate-900">Total</strong>
        <strong class="text-2xl font-bold text-slate-900">{{ formatCurrency(total) }}</strong>
      </div>
      <button type="button" :disabled="loading" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" @click="finalizeCheckout">
        {{ loading ? 'Finalizando...' : 'Finalizar compra' }} <ArrowRight :size="16" />
      </button>
    </section>
  </PageContainer>
</template>
