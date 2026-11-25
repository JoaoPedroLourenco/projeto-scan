<script setup>
import { onMounted, ref } from 'vue'
import PageContainer from './ui/PageContainer.vue'
import ScannerBarCode from './Produtos/ScannerBarCode.vue'
import { lookupBarCode, showAllProducts } from '../services/productService.js'
import { addToCart } from '../services/shoppingService.js'
import { formatCurrency } from '../utils/masks.js'

const manualCode = ref('')
const loading = ref(false)
const error = ref('')
const successNotice = ref('')
const allProducts = ref([])

async function loadProducts() {
  try {
    const res = await showAllProducts()
    allProducts.value = Array.isArray(res.data) ? res.data : res.data?.data ?? []
  } catch (err) {
    console.error('Erro ao carregar catálogo para scanner:', err)
  }
}

async function handleBarcode(code) {
  if (!code || loading.value) return
  error.value = ''
  loading.value = true

  try {
    let productFound = null
    try {
      const res = await lookupBarCode(code)
      productFound = res?.data?.product || res?.data
    } catch {
      /* fallback no catálogo abaixo */
    }
    if (!productFound?.id) {
      productFound = allProducts.value.find((p) => String(p.barcode) === String(code))
    }

    if (productFound) {
      const added = await addToCart(productFound, 1)
      if (added) {
        successNotice.value = `${productFound.name} (${formatCurrency(productFound.price)}) colocado na sacola.`
        setTimeout(() => (successNotice.value = ''), 4000)
      }
    } else {
      error.value = `Nenhum produto cadastrado com o código ${code}.`
    }
  } catch (err) {
    const backendErrors = err.response?.data?.errors
    error.value =
      (backendErrors && Object.values(backendErrors).flat()[0]) ||
      err.response?.data?.msg ||
      err.response?.data?.message ||
      'Erro ao consultar código de barras.'
  } finally {
    loading.value = false
  }
}

function useManualCode() {
  const code = manualCode.value.trim()
  if (code) {
    handleBarcode(code)
    manualCode.value = ''
  }
}

onMounted(loadProducts)
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Scanner de Produtos</h1>
      <p class="mt-1 text-xs text-slate-500">Aponte a câmera para bipar o código de barras ou digite manualmente para colocar na sacola.</p>
    </header>

    <div v-if="successNotice" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800 shadow-sm">
      {{ successNotice }}
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
      <ScannerBarCode @codigo-lido="handleBarcode" />
      <form @submit.prevent="useManualCode" class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
        <input v-model="manualCode" type="text" inputmode="numeric" placeholder="Ou digite o código de barras (EAN)..." aria-label="Digite o código de barras" class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs outline-none focus:border-blue-400 focus:bg-white" />
        <button type="submit" :disabled="loading" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60">
          {{ loading ? 'Adicionando...' : 'Adicionar' }}
        </button>
      </form>
      <p v-if="error" class="mt-3 text-xs text-rose-600">{{ error }}</p>
    </section>
  </PageContainer>
</template>
