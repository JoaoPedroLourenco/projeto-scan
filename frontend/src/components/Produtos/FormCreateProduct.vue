<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { createProducts, getAllergensFromProducts, getCategoriesFromProducts, registerStockEntry, saveProductStock, showAllProducts } from '../../services/productService.js'
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
const categories = ref([])
const knownAllergens = ref([])

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

// Preenche o formulário quando o código de barras é encontrado
watch(produto, (resultado) => {
  const prod = resultado?.product
  if (!prod) return

  form.category_id = prod.category_id
  form.category_name = prod.category?.name || ''
  form.name = prod.name
  form.brand = prod.brand || ''
  form.description = prod.description || ''
  form.barcode = prod.barcode || ''
  form.price = prod.price ? Number(prod.price) : ''

  allergens.value = (prod.allergens || []).map((item) => {
    if (typeof item === 'object') return item
    return knownAllergens.value.find((a) => a.id == item) || item
  })
})

async function registerProduct() {
  const precoFinal = Number(form.price)
  if (!precoFinal || precoFinal <= 0) {
    error.value = 'Informe um preço de venda válido (mínimo R$ 1,00).'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const idsAlergenos = allergens.value.map((a) => (typeof a === 'object' ? a.id : Number(a)))
    const res = await createProducts({
      category_id: Number(form.category_id),
      name: form.name,
      barcode: form.barcode,
      description: form.description,
      price: precoFinal,
      min_stock: 5,
      is_active: Boolean(form.is_active),
      allergens: idsAlergenos,
    })

    if (res?.product?.id && Number(form.initial_stock) > 0 && form.batch_code && form.expiration) {
      try {
        await registerStockEntry({
          product_id: res.product.id,
          batch_code: form.batch_code,
          quantity: Number(form.initial_stock),
          expiration_date: form.expiration,
          unit_price: precoFinal,
        })
      } catch {
        /* fallback local abaixo se /stock/entry não estiver disponível */
      }
    }

    saveProductStock(res?.product?.id, {
      stock_quantity: form.initial_stock,
      batch_code: form.batch_code,
      expiration_date: form.expiration,
    })

    message.value = 'Produto cadastrado com sucesso.'
    setTimeout(() => router.push('/produtos'), 900)
  } catch (err) {
    const backendErrors = err.response?.data?.errors
    error.value =
      (backendErrors && Object.values(backendErrors).flat()[0]) ||
      err.response?.data?.msg ||
      err.response?.data?.message ||
      'Não foi possível cadastrar o produto.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const res = await showAllProducts()
    const lista = Array.isArray(res.data) ? res.data : res.data?.data ?? []
    categories.value = getCategoriesFromProducts(lista)
    knownAllergens.value = getAllergensFromProducts(lista)
  } catch {
    /* segue sem cache */
  }
})
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Cadastro de Produtos</h1>
      <p class="mt-1 text-xs text-slate-500">Bipe ou digite o código de barras para preencher os dados.</p>
    </header>

    <div class="mb-5 flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
      <InputBarCode />
      <button type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-xs font-medium text-blue-600" @click="scannerOpen = !scannerOpen">
        {{ scannerOpen ? 'Fechar scanner' : 'Scanner' }}
      </button>
    </div>
    <ScannerBarCode v-if="scannerOpen" @codigo-lido="form.barcode = $event; scannerOpen = false" />
    <p v-if="message" class="my-3 text-xs text-emerald-700">{{ message }}</p>
    <p v-if="error" class="my-3 text-xs text-rose-700">{{ error }}</p>

    <form class="grid items-start gap-5 lg:grid-cols-[1.2fr_0.9fr]" @submit.prevent="registerProduct">
      <ProductDetailsCard :form="form" :allergens="allergens" :categories="categories" @update:name="form.name = $event" @update:brand="form.brand = $event" @update:category="form.category_id = $event" @update:description="form.description = $event" @update:barcode="form.barcode = $event" />
      <ProductPricingStockCards v-model:price="form.price" v-model:cost="form.cost" v-model:margin="form.margin" v-model:initial-stock="form.initial_stock" v-model:batch-code="form.batch_code" v-model:expiration="form.expiration" />
      <div class="flex justify-end lg:col-span-2">
        <button class="rounded-xl bg-blue-600 px-6 py-3 text-xs font-semibold text-white disabled:opacity-60" :disabled="loading">{{ loading ? 'Salvando...' : 'Salvar Produto' }}</button>
      </div>
    </form>
  </PageContainer>
</template>
