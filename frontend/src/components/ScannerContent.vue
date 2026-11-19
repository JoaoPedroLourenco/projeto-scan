<script setup>
import { ref } from 'vue'
import PageContainer from './ui/PageContainer.vue'
import ScannerBarCode from './Produtos/ScannerBarCode.vue'

const barcode = ref('')
const manualCode = ref('')

function useManualCode() {
  barcode.value = manualCode.value.trim()
}
</script>

<template>
  <PageContainer>
    <header class="mb-5">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Scanner</h1>
      <p class="mt-1 text-xs text-slate-500">
        Aponte a câmera para o código de barras ou digite o código manualmente.
      </p>
    </header>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
      <ScannerBarCode @codigo-lido="barcode = $event" />
      <p v-if="barcode" class="mt-3 text-xs text-slate-600" role="status">
        Código lido: <strong class="font-mono text-slate-900">{{ barcode }}</strong>
      </p>
      <form @submit.prevent="useManualCode" class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
        <input
          v-model="manualCode"
          type="text"
          inputmode="numeric"
          placeholder="Digite o código de barras..."
          aria-label="Digite o código de barras"
          class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs outline-none focus:border-blue-400 focus:bg-white"
        />
        <button
          type="submit"
          class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-blue-700"
        >
          Usar código
        </button>
      </form>
    </section>
  </PageContainer>
</template>
