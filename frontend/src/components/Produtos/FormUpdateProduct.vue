<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getCategoriesFromProducts, getProductStockMap, saveProductStock, searchProductById, showAllProducts, updateProduct } from '../../services/productService.js'
import ProductDetailsCard from './ProductDetailsCard.vue'
import ProductPricingStockCards from './ProductPricingStockCards.vue'
import PageContainer from '../ui/PageContainer.vue'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const allergens = ref([])
const categories = ref([])

const form = reactive({
  category_id: '', category_name: '', name: '', brand: '', description: '', barcode: '',
  price: '', cost: '', margin: '', initial_stock: 0, batch_code: '', expiration: '', is_active: true,
})

// Calcula o preço de venda quando o usuário digita custo ou margem
watch(() => [form.cost, form.margin], () => {
  const custo = Number(form.cost)
  const margem = Number(form.margin) || 0
  if (custo > 0) {
    form.price = Number((custo * (1 + margem / 100)).toFixed(2))
  }
})

async function loadProduct() {
  try {
    const res = await searchProductById(Number(route.params.id))
    const prod = res?.product ?? res

    // Carrega nomes das categorias disponíveis
    try {
      const todas = await showAllProducts()
      const lista = Array.isArray(todas.data) ? todas.data : todas.data?.data ?? []
      categories.value = getCategoriesFromProducts(lista, prod.category)
    } catch {
      categories.value = prod.category ? [prod.category] : []
    }

    allergens.value = prod.allergens || []
    const estoque = getProductStockMap()[prod.id] || {}

    form.category_id = prod.category_id
    form.category_name = prod.category?.name || ''
    form.name = prod.name
    form.brand = prod.brand || ''
    form.description = prod.description || ''
    form.barcode = prod.barcode || ''
    form.price = Number(prod.price || 0)
    form.initial_stock = estoque.stock_quantity ?? 0
    form.batch_code = estoque.batch_code ?? ''
    form.expiration = estoque.expiration_date ?? ''
    form.is_active = prod.is_active
  } catch (err) {
    error.value = err.response?.data?.msg || err.response?.data?.message || 'Não foi possível carregar o produto.'
  } finally {
    loading.value = false
  }
}

async function saveProduct() {
  const precoFinal = Number(form.price)
  if (!precoFinal || precoFinal <= 0) {
    error.value = 'Informe um preço de venda válido (mínimo R$ 1,00).'
    return
  }

  saving.value = true
  error.value = ''

  try {
    await updateProduct(Number(route.params.id), {
      category_id: Number(form.category_id),
      name: form.name,
      barcode: form.barcode,
      description: form.description,
      price: precoFinal,
      min_stock: 5,
      is_active: Boolean(form.is_active),
    })

    saveProductStock(route.params.id, {
      stock_quantity: form.initial_stock,
      batch_code: form.batch_code,
      expiration_date: form.expiration,
    })

    router.push('/produtos')
  } catch (err) {
    const backendErrors = err.response?.data?.errors
    error.value =
      (backendErrors && Object.values(backendErrors).flat()[0]) ||
      err.response?.data?.msg ||
      err.response?.data?.message ||
      'Não foi possível salvar o produto.'
  } finally {
    saving.value = false
  }
}

onMounted(loadProduct)
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Editar Produto</h1>
      <p class="mt-1 text-xs text-slate-500">Altere os dados e salve o produto.</p>
    </header>
    <p v-if="loading" class="text-xs text-slate-500">Carregando produto...</p>
    <p v-if="error" class="mb-4 text-xs text-rose-700">{{ error }}</p>
    <form v-if="!loading" class="grid items-start gap-5 lg:grid-cols-[1.2fr_0.9fr]" @submit.prevent="saveProduct">
      <ProductDetailsCard :form="form" :allergens="allergens" :categories="categories" @update:name="form.name = $event" @update:brand="form.brand = $event" @update:category="form.category_id = $event" @update:description="form.description = $event" @update:barcode="form.barcode = $event" />
      <ProductPricingStockCards v-model:price="form.price" v-model:cost="form.cost" v-model:margin="form.margin" v-model:initial-stock="form.initial_stock" v-model:batch-code="form.batch_code" v-model:expiration="form.expiration" />
      <div class="flex justify-end lg:col-span-2">
        <button class="rounded-xl bg-blue-600 px-6 py-3 text-xs font-semibold text-white disabled:opacity-60" :disabled="saving">{{ saving ? 'Salvando...' : 'Salvar Alterações' }}</button>
      </div>
    </form>
  </PageContainer>
</template>
