<script setup>
import { ref } from 'vue'
import CatalogProductCard from './CatalogProductCard.vue'
import PageContainer from '../ui/PageContainer.vue'

const products = ref([])
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Catálogo de Produtos</h1>
      <p class="mt-1 text-xs text-slate-500">
        Explore os produtos disponíveis e confira preços e ofertas.
      </p>
    </header>

    <nav class="mb-7 flex flex-wrap gap-2" aria-label="Categorias de produtos">
      <button type="button" class="rounded-full bg-blue-600 px-4 py-2 text-xs font-medium text-white">
        Todos
      </button>
    </nav>

    <div v-if="products.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
      <CatalogProductCard v-for="product in products" :key="product.id" :product="product" />
    </div>
  </PageContainer>
</template>
