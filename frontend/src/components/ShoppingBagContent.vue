<script setup>
import { computed, onMounted, ref } from 'vue'
import { ArrowRight, Minus, Plus, Trash2 } from '@lucide/vue'
import PageContainer from './ui/PageContainer.vue'
import { formatCurrency } from '../utils/masks.js'
import { clearCart, getCartItems, removeCartItem, updateCartQuantity } from '../services/shoppingService.js'

const items = ref([])
const checkoutSuccess = ref(false)
const total = computed(() =>
  items.value.reduce((acc, i) => acc + Number(i.unit_price || i.product?.price || 0) * (i.quantity || 1), 0),
)

function finalizeCheckout() {
  if (!items.value.length) return
  clearCart()
  items.value = []
  checkoutSuccess.value = true
}

onMounted(() => {
  items.value = getCartItems()
  window.addEventListener('cart-updated', () => (items.value = getCartItems()))
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

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <p v-if="!items.length && !checkoutSuccess" class="px-5 py-16 text-center text-sm text-slate-500">
        Sua sacola está vazia. Adicione produtos pelo catálogo ou pelo scanner.
      </p>
      <article v-for="item in items" :key="item.product_id" class="flex items-center gap-3 border-b border-slate-100 px-4 py-4 last:border-0 sm:px-5">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
          <img v-if="item.product?.image_url" :src="item.product.image_url" :alt="item.product?.name" class="h-full w-full object-contain p-1" />
        </span>
        <div class="min-w-0 flex-1">
          <strong class="block truncate text-xs font-semibold text-slate-900">{{ item.product?.name || 'Produto' }}</strong>
          <span class="mt-0.5 block text-[10px] text-slate-400">{{ item.product?.category?.name || 'Geral' }}</span>
          <strong class="mt-1 block text-xs text-slate-900">{{ formatCurrency(item.unit_price || item.product?.price) }}</strong>
        </div>
        <div class="flex items-center rounded-lg border border-slate-200">
          <button class="p-2 text-slate-500 hover:text-blue-600" aria-label="Diminuir quantidade" @click="items = updateCartQuantity(item.product_id, item.quantity - 1)"><Minus :size="13" /></button>
          <span class="min-w-7 text-center text-xs font-medium">{{ item.quantity }}</span>
          <button class="p-2 text-slate-500 hover:text-blue-600" aria-label="Aumentar quantidade" @click="items = updateCartQuantity(item.product_id, item.quantity + 1)"><Plus :size="13" /></button>
        </div>
        <button class="p-2 text-slate-400 hover:text-rose-600" aria-label="Remover produto" @click="items = removeCartItem(item.product_id)"><Trash2 :size="15" /></button>
      </article>
    </section>

    <section v-if="items.length" class="mt-6 space-y-3">
      <div class="flex justify-between text-xs text-slate-500"><span>Subtotal</span><span>{{ formatCurrency(total) }}</span></div>
      <div class="flex items-center justify-between border-t border-slate-100 pt-3">
        <strong class="text-sm text-slate-900">Total</strong>
        <strong class="text-2xl font-bold text-slate-900">{{ formatCurrency(total) }}</strong>
      </div>
      <button type="button" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700" @click="finalizeCheckout">
        Finalizar compra <ArrowRight :size="16" />
      </button>
    </section>
  </PageContainer>
</template>
