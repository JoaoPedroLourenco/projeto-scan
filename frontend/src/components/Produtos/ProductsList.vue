<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Plus } from '@lucide/vue'
import { deleteProduct, showAllProducts } from '../../services/productService.js'
import DeleteConfirmation from './DeleteConfirmation.vue'
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
const deleting = ref(null)

const categories = computed(() => [
  ...new Set(products.value.map((product) => product.category?.name).filter(Boolean)),
])
const filteredProducts = computed(() => {
  const term = search.value.trim().toLowerCase()
  return products.value.filter((product) =>
    `${product.name} ${product.barcode}`.toLowerCase().includes(term) &&
      (!category.value || product.category?.name === category.value),
  )
})
const totalPages = computed(() => Math.max(1, Math.ceil(filteredProducts.value.length / pageSize)))
const visibleProducts = computed(() =>
  filteredProducts.value.slice((page.value - 1) * pageSize, page.value * pageSize),
)

async function loadProducts() {
  loading.value = true
  error.value = ''
  try {
    const response = await showAllProducts()
    products.value = response.data ?? []
  } catch (cause) {
    error.value = cause.response?.data?.message || 'Não foi possível carregar os produtos.'
  } finally {
    loading.value = false
  }
}

watch(search, () => (page.value = 1))

function changePage(nextPage) {
  page.value = nextPage
}

async function confirmDelete() {
  if (!deleting.value) return
  try {
    await deleteProduct(deleting.value.id)
    products.value = products.value.filter((product) => product.id !== deleting.value.id)
    deleting.value = null
  } catch (cause) {
    error.value = cause.response?.data?.message || 'Não foi possível excluir o produto.'
  }
}

onMounted(loadProducts)
</script>

<template>
  <PageContainer>
    <header class="mb-5 flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Gestão de Estoque</h1>
        <p class="mt-1 text-xs text-slate-500">
          Monitore níveis, lotes e validade dos produtos cadastrados.
        </p>
      </div>
      <RouterLink
        to="/produtos/registro"
        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-blue-700"
        ><Plus :size="15" /> Novo Produto</RouterLink
      >
    </header>

    <form
      @submit.prevent
      class="mb-5 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:flex-row sm:items-center"
    >
      <input
        v-model="search"
        placeholder="Buscar por nome ou código EAN..."
        aria-label="Buscar por nome ou EAN"
        class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs outline-none focus:border-blue-400 focus:bg-white"
      />
      <select
        v-model="category"
        aria-label="Filtrar categoria"
        class="min-w-28 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700"
      >
        <option value="">Todas as categorias</option>
        <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
      </select>
    </form>

    <p
      v-if="error"
      class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700"
    >
      {{ error }}
      <button type="button" @click="loadProducts" class="ml-1 font-semibold underline">
        Tentar novamente
      </button>
    </p>

    <ProductsTable
      :products="visibleProducts"
      :loading="loading"
      :page="page"
      :last-page="totalPages"
      :total="filteredProducts.length"
      @edit="router.push(`/produtos/editar/${$event.id}`)"
      @delete="deleting = $event"
      @change-page="changePage"
    />

    <DeleteConfirmation
      v-if="deleting"
      :product-name="deleting.name"
      :loading="false"
      @confirm="confirmDelete"
      @cancel="deleting = null"
    />
  </PageContainer>
</template>
