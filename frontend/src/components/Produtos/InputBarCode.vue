<script setup>
import { ref } from 'vue'
import { lookupBarCode } from '../../services/productService.js'
import { useProduct } from '../../composables/useProduct'

const { definirProduto } = useProduct()

const barcode = ref('')
const loading = ref(false)
const notFoundMessage = ref('')

async function lookupByBarcode() {
  if (!barcode.value.trim() || loading.value) return
  loading.value = true
  notFoundMessage.value = ''
  try {
    const response = await lookupBarCode(barcode.value.trim())
    definirProduto(response.data)
  } catch (error) {
    console.error('Erro ao buscar produto:', error.response?.data ?? error.message)
    notFoundMessage.value = error.response?.data?.message || 'Código de barras não encontrado.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-w-0 flex-1">
    <form class="flex items-center gap-2" @submit.prevent="lookupByBarcode">
      <input
        type="text"
        name="barcode"
        id="barcode"
        v-model="barcode"
        placeholder="Digite o código de barras (EAN)..."
        class="min-w-0 flex-1 bg-transparent px-2 py-2 text-sm text-slate-700 outline-none placeholder:text-slate-400"
      />
      <button
        type="submit"
        :disabled="loading"
        class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
      >
        {{ loading ? 'Buscando...' : 'Buscar' }}
      </button>
    </form>
    <p v-if="notFoundMessage" class="px-2 pt-1 text-[11px] text-rose-600">
      {{ notFoundMessage }}
    </p>
  </div>
</template>
