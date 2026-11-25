<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { showAllProducts } from '../../services/productService.js'
import { addToCart } from '../../services/shoppingService.js'
import CatalogProductCard from './CatalogProductCard.vue'
import PageContainer from '../ui/PageContainer.vue'

const route = useRoute()
const products = ref([])
const selectedCategory = ref('')
const cartFeedback = ref('')

const categories = computed(() => {
  const names = products.value.map((p) => p.category?.name).filter(Boolean)
  return [...new Set(names)]
})

const filteredProducts = computed(() => {
  const query = String(route.query.busca || '').trim().toLowerCase()
  return products.value.filter((p) => {
    const matchesSearch = !query || `${p.name} ${p.barcode || ''}`.toLowerCase().includes(query)
    const matchesCategory = !selectedCategory.value || p.category?.name === selectedCategory.value
    return matchesSearch && matchesCategory
  })
})

async function loadProducts() {
  try {
    const response = await showAllProducts()
    const result = response.data
    products.value = Array.isArray(result) ? result : result.data ?? []
  } catch (error) {
    console.error('Erro ao carregar produtos do catálogo:', error.response?.data ?? error.message)
  }
}

async function handleAddToCart(product) {
  try {
    const added = await addToCart(product, 1)
    if (added) {
      cartFeedback.value = `${product.name} adicionado à sacola.`
      setTimeout(() => (cartFeedback.value = ''), 3000)
    }
  } catch (err) {
    const backendErrors = err.response?.data?.errors
    cartFeedback.value =
      (backendErrors && Object.values(backendErrors).flat()[0]) ||
      err.response?.data?.msg ||
      err.response?.data?.message ||
      'Não foi possível adicionar o produto à sacola.'
    setTimeout(() => (cartFeedback.value = ''), 3000)
  }
}

onMounted(loadProducts)
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Catálogo de Produtos</h1>
      <p class="mt-1 text-xs text-slate-500">
        Explore os produtos disponíveis e confira preços e ofertas.
      </p>
    </header>

    <p
      v-if="cartFeedback"
      class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800"
    >
      {{ cartFeedback }}
    </p>

    <nav class="mb-7 flex flex-wrap gap-2" aria-label="Categorias de produtos">
      <button
        type="button"
        @click="selectedCategory = ''"
        :class="!selectedCategory ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
        class="rounded-full px-4 py-2 text-xs font-medium transition"
      >
        Todos
      </button>
      <button
        v-for="cat in categories"
        :key="cat"
        type="button"
        @click="selectedCategory = cat"
        :class="selectedCategory === cat ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
        class="rounded-full px-4 py-2 text-xs font-medium transition"
      >
        {{ cat }}
      </button>
    </nav>

    <div v-if="filteredProducts.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
      <CatalogProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
        @add="handleAddToCart"
      />
    </div>
    <p v-else class="py-12 text-center text-xs text-slate-500">
      Nenhum produto encontrado.
    </p>
  </PageContainer>
</template>
