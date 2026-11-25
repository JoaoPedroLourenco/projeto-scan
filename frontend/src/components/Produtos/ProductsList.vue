<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Plus } from '@lucide/vue'
import { deleteProduct, getProductStockMap, getStockBatches, removeProductStock, showAllProducts } from '../../services/productService.js'
import ProductsTable from './ProductsTable.vue'
import PageContainer from '../ui/PageContainer.vue'

const router = useRouter()
const products = ref([])
const loading = ref(false)
const error = ref('')
const search = ref('')
const category = ref('')
const page = ref(1)
const pageSize = 15

const categories = computed(() => [...new Set(products.value.map((p) => p.category?.name).filter(Boolean))])
const filteredProducts = computed(() => {
  const term = search.value.trim().toLowerCase()
  return products.value.filter(
    (p) => `${p.name} ${p.barcode}`.toLowerCase().includes(term) && (!category.value || p.category?.name === category.value),
  )
})
const totalPages = computed(() => Math.max(1, Math.ceil(filteredProducts.value.length / pageSize)))
const visibleProducts = computed(() => filteredProducts.value.slice((page.value - 1) * pageSize, page.value * pageSize))

async function loadProducts() {
  loading.value = true
  error.value = ''
  try {
    const res = await showAllProducts()
    const list = Array.isArray(res.data) ? res.data : res.data?.data ?? []
    const stockMap = { ...getProductStockMap() }
    try {
      const batches = await getStockBatches()
      for (const b of batches) {
        if (b?.product_id) {
          stockMap[String(b.product_id)] = {
            stock_quantity: (stockMap[String(b.product_id)]?.stock_quantity || 0) + Number(b.quantity || 0),
            batch_code: b.batch_code || '',
            expiration_date: b.expiration_date || '',
          }
        }
      }
    } catch {
      /* mantém dados locais se /stock não estiver disponível */
    }
    products.value = list.map((p) => ({ ...stockMap[String(p.id)], ...p }))
  } catch (err) {
    error.value = err.response?.data?.msg || err.response?.data?.message || 'Não foi possível carregar os produtos.'
  } finally {
    loading.value = false
  }
}

watch(search, () => (page.value = 1))

async function handleDelete(product) {
  if (!product?.id) return
  if (!window.confirm(`Tem certeza que deseja excluir o produto "${product.name}"?`)) return
  try {
    await deleteProduct(product.id)
    products.value = products.value.filter((p) => p.id !== product.id)
    removeProductStock(product.id)
  } catch (err) {
    error.value = err.response?.data?.message || 'Não foi possível excluir o produto.'
  }
}

onMounted(loadProducts)
</script>

<template>
  <PageContainer>
    <header class="mb-5 flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Gestão de Estoque</h1>
        <p class="mt-1 text-xs text-slate-500">Monitore níveis, lotes e validade dos produtos cadastrados.</p>
      </div>
      <RouterLink to="/produtos/registro" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-blue-700">
        <Plus :size="15" /> Novo Produto
      </RouterLink>
    </header>

    <form @submit.prevent class="mb-5 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:flex-row sm:items-center">
      <input v-model="search" placeholder="Buscar por nome ou código EAN..." aria-label="Buscar por nome ou EAN" class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs outline-none focus:border-blue-400 focus:bg-white" />
      <select v-model="category" aria-label="Filtrar categoria" class="min-w-28 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700">
        <option value="">Todas as categorias</option>
        <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
      </select>
    </form>

    <p v-if="error" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
      {{ error }}
      <button type="button" @click="loadProducts" class="ml-1 font-semibold underline">Tentar novamente</button>
    </p>

    <ProductsTable
      :products="visibleProducts"
      :loading="loading"
      :page="page"
      :last-page="totalPages"
      :total="filteredProducts.length"
      @edit="router.push(`/produtos/editar/${$event.id}`)"
      @delete="handleDelete"
      @change-page="page = $event"
    />
  </PageContainer>
</template>
