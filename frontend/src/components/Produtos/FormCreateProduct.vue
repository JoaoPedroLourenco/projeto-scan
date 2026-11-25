<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { createProducts } from '../../services/productService.js'
import { useProduct } from '../../composables/useProduct.js'
import InputBarCode from './InputBarCode.vue'
import ScannerBarCode from './ScannerBarCode.vue'
import ProductDetailsCard from './ProductDetailsCard.vue'
import ProductPricingStockCards from './ProductPricingStockCards.vue'
import PageContainer from '../ui/PageContainer.vue'

const router = useRouter()
const { produto } = useProduct()
const loading = ref(false)
const scannerOpen = ref(false)
const message = ref('')
const error = ref('')
const allergens = ref([])
const form = reactive({
  category_id: '', name: '', brand: '', description: '', barcode: '',
  price: 0, cost: 0, margin: 0, min_stock: 5, initial_stock: 0,
  batch_code: '', expiration: '', is_active: true,
})

const suggestedPrice = computed(() =>
  form.cost ? form.cost * (1 + form.margin / 100) : Number(form.price),
)
const profit = computed(() => suggestedPrice.value - Number(form.cost))

watch(produto, (result) => {
  const product = result?.product
  if (!product) return
  Object.assign(form, {
    category_id: product.category_id,
    name: product.name,
    brand: product.brand || '',
    description: product.description || '',
    barcode: product.barcode || '',
    price: Number(product.price || 0),
    min_stock: Number(product.min_stock || 5),
  })
  allergens.value = product.allergens || []
})

async function registerProduct() {
  loading.value = true
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
    await createProducts(productInfo)
    message.value = 'Produto cadastrado com sucesso.'
    window.setTimeout(() => router.push('/produtos'), 900)
  } catch (cause) {
    error.value = cause.response?.data?.message || 'Não foi possível cadastrar o produto.'
  } finally {
    loading.value = false
  }
}

function handleBarcode(code) {
  form.barcode = code
  scannerOpen.value = false
}
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Cadastro de Produtos</h1>
      <p class="mt-1 text-xs text-slate-500">Bipe ou digite o código de barras para preencher os dados.</p>
    </header>

    <div class="mb-5 flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
      <InputBarCode />
      <button
        type="button"
        class="rounded-lg border border-slate-200 px-4 py-2.5 text-xs font-medium text-blue-600"
        @click="scannerOpen = !scannerOpen"
      >
        {{ scannerOpen ? 'Fechar scanner' : 'Scanner' }}
      </button>
    </div>
    <ScannerBarCode v-if="scannerOpen" @codigo-lido="handleBarcode" />
    <p v-if="message" class="my-3 text-xs text-emerald-700">{{ message }}</p>
    <p v-if="error" class="my-3 text-xs text-rose-700">{{ error }}</p>

    <form class="grid items-start gap-5 lg:grid-cols-[1.2fr_0.9fr]" @submit.prevent="registerProduct">
      <ProductDetailsCard
        :form="form"
        :allergens="allergens"
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
        <button class="rounded-xl bg-blue-600 px-6 py-3 text-xs font-semibold text-white disabled:opacity-60" :disabled="loading">
          {{ loading ? 'Salvando...' : 'Salvar Produto' }}
        </button>
      </div>
    </form>
  </PageContainer>
</template>
