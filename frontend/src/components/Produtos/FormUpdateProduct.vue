<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { searchProductById, updateProduct } from '../../services/productService.js'
import ProductDetailsCard from './ProductDetailsCard.vue'
import ProductPricingStockCards from './ProductPricingStockCards.vue'
import PageContainer from '../ui/PageContainer.vue'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const form = reactive({
  category_id: '', name: '', brand: '', description: '', barcode: '',
  price: 0, cost: 0, margin: 0, min_stock: 5, initial_stock: 0,
  batch_code: '', expiration: '', is_active: true,
})

const suggestedPrice = computed(() =>
  form.cost ? form.cost * (1 + form.margin / 100) : Number(form.price),
)
const profit = computed(() => suggestedPrice.value - Number(form.cost))

async function loadProduct() {
  try {
    const response = await searchProductById(Number(route.params.id))
    const product = response?.product ?? response
    Object.assign(form, {
      category_id: product.category_id,
      name: product.name,
      brand: product.brand || '',
      description: product.description || '',
      barcode: product.barcode || '',
      price: Number(product.price || 0),
      min_stock: Number(product.min_stock || 5),
      is_active: product.is_active,
    })
  } catch (cause) {
    error.value = cause.response?.data?.message || 'Não foi possível carregar o produto.'
  } finally {
    loading.value = false
  }
}

async function saveProduct() {
  saving.value = true
  error.value = ''
  const productInfo = {
    category_id: Number(form.category_id),
    name: form.name,
    barcode: form.barcode,
    description: form.description,
    price: Number(suggestedPrice.value),
    min_stock: Number(form.min_stock),
  }

  try {
    await updateProduct(Number(route.params.id), productInfo)
    router.push('/produtos')
  } catch (cause) {
    error.value = cause.response?.data?.message || 'Não foi possível salvar o produto.'
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
      <ProductDetailsCard
        :form="form"
        @update:name="form.name = $event"
        @update:brand="form.brand = $event"
        @update:category="form.category_id = $event"
        @update:description="form.description = $event"
        @update:barcode="form.barcode = $event"
      />
      <ProductPricingStockCards
        v-model:cost="form.cost"
        v-model:margin="form.margin"
        :suggested-price="suggestedPrice"
        :profit="profit"
        v-model:initial-stock="form.initial_stock"
        v-model:min-stock="form.min_stock"
        v-model:batch-code="form.batch_code"
        v-model:expiration="form.expiration"
      />
      <div class="flex items-center justify-between border-t border-slate-200 pt-4 lg:col-span-2">
        <label class="flex items-center gap-2 text-xs text-slate-700">
          <input v-model="form.is_active" type="checkbox" />
          Disponibilizar no autoatendimento imediatamente
        </label>
        <button class="rounded-xl bg-blue-600 px-6 py-3 text-xs font-semibold text-white disabled:opacity-60" :disabled="saving">
          {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
        </button>
      </div>
    </form>
  </PageContainer>
</template>
