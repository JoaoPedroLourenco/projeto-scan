<script setup>
import { ref } from 'vue'
import { lookupBarCode } from '../../services/productService.js'
import { useProduct } from '../../composables/useProduct'

const { definirProduto } = useProduct()

const barcode = ref('')
const produto = ref(null)

async function lookupByBarcode() {
  try {
    const response = await lookupBarCode(barcode.value)

    produto.value = response.data

    console.log(response.data)
    definirProduto(response.data)
  } catch (error) {
    console.error(error.response.data)
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
        class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700"
      >
        Buscar
      </button>
    </form>
    <p v-if="produto" class="px-2 text-[11px] text-emerald-700">{{ produto.name }}</p>
  </div>
</template>
