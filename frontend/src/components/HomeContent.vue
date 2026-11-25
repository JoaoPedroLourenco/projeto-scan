<script setup>
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { ArrowRight, ListChecks, ShoppingBag } from '@lucide/vue'
import CatalogProductCard from './Produtos/CatalogProductCard.vue'
import PageContainer from './ui/PageContainer.vue'
import { showAllProducts } from '../services/productService.js'
import { addToCart } from '../services/shoppingService.js'

const featuredProducts = ref([])
const cartFeedback = ref('')

async function handleAddToCart(product) {
  try {
    const added = await addToCart(product, 1)
    if (added) {
      cartFeedback.value = `${product.name} adicionado à sacola.`
      setTimeout(() => (cartFeedback.value = ''), 3000)
    }
  } catch {
    cartFeedback.value = 'Não foi possível adicionar o produto à sacola.'
    setTimeout(() => (cartFeedback.value = ''), 3000)
  }
}

onMounted(async () => {
  try {
    const res = await showAllProducts()
    const list = Array.isArray(res.data) ? res.data : res.data?.data ?? []
    featuredProducts.value = list.slice(0, 5)
  } catch (err) {
    console.error('Erro ao carregar produtos da página inicial:', err.response?.data ?? err.message)
  }
})
</script>

<template>
  <PageContainer>
    <header class="mb-7">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Início</h1>
      <p class="mt-1 text-xs text-slate-500">Acompanhe seus produtos e explore o catálogo.</p>
    </header>

    <section class="mb-3 flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-3.5">
      <div>
        <strong class="block text-sm font-semibold text-slate-900">Escanear Produto</strong>
        <p class="mt-1 text-xs text-slate-500">Aponte o código de barras ou digite o código numérico no teclado.</p>
      </div>
      <RouterLink to="/scanner" class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">
        Abrir Scanner
      </RouterLink>
    </section>

    <p v-if="cartFeedback" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800">{{ cartFeedback }}</p>

    <div class="mb-10 grid gap-3 sm:grid-cols-2">
      <section class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4">
        <h2 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900"><ShoppingBag :size="16" /> Minha Sacola Ativa</h2>
        <RouterLink to="/sacola" class="w-full rounded-lg bg-slate-100 py-2.5 text-center text-xs font-medium text-slate-700 transition hover:bg-slate-200">
          Ver Sacola <ArrowRight class="ml-1 inline" :size="14" />
        </RouterLink>
      </section>
      <section class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4">
        <h2 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900"><ListChecks :size="16" /> Lista de Compras</h2>
        <RouterLink to="/lista" class="w-full rounded-lg bg-slate-100 py-2.5 text-center text-xs font-medium text-slate-700 transition hover:bg-slate-200">
          Continuar Lista <ArrowRight class="ml-1 inline" :size="14" />
        </RouterLink>
      </section>
    </div>

    <section>
      <header class="mb-3 flex items-end justify-between">
        <div>
          <h2 class="text-sm font-semibold text-slate-900">Produtos em destaque</h2>
          <p class="mt-1 text-[11px] text-slate-500">Produtos selecionados para você.</p>
        </div>
        <RouterLink to="/catalogo" class="text-[11px] font-medium text-blue-600 hover:text-blue-700">
          Ver catálogo <ArrowRight class="ml-1 inline" :size="13" />
        </RouterLink>
      </header>
      <div v-if="featuredProducts.length" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <CatalogProductCard v-for="product in featuredProducts" :key="product.id" :product="product" @add="handleAddToCart" />
      </div>
    </section>
  </PageContainer>
</template>
