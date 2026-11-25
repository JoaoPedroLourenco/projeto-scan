<script setup>
defineProps({
  price: { type: [Number, String], default: '' },
  cost: { type: [Number, String], default: '' },
  margin: { type: [Number, String], default: '' },
  initialStock: { type: [Number, String], default: '' },
  batchCode: { type: String, default: '' },
  expiration: { type: String, default: '' },
})
const emit = defineEmits(['update:price', 'update:cost', 'update:margin', 'update:initialStock', 'update:batchCode', 'update:expiration'])
</script>

<template>
  <div class="space-y-4">
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
      <h2 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-800">Formação de Preços e Margem</h2>
      <div class="grid grid-cols-2 gap-3">
        <label class="text-xs font-medium text-slate-600">Preço de Custo (R$)<input :value="cost" @input="emit('update:cost', $event.target.value)" type="number" min="0" step="0.01" placeholder="0,00" class="product-input" /></label>
        <label class="text-xs font-medium text-slate-600">Margem Alvo (%)<input :value="margin" @input="emit('update:margin', $event.target.value)" type="number" min="0" step="1" placeholder="0" class="product-input" /></label>
      </div>
      <div class="mt-3">
        <label class="block text-xs font-medium text-slate-700">
          Preço de Venda (R$) *
          <input :value="price" @input="emit('update:price', $event.target.value)" type="number" min="0.01" step="0.01" required placeholder="0,00" class="product-input mt-1 w-full font-semibold text-slate-900" />
        </label>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
      <h2 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-800">Estoque &amp; Controle de Validade</h2>
      <div class="grid grid-cols-2 gap-3">
        <label class="text-xs font-medium text-slate-600 sm:col-span-2">
          Estoque Inicial
          <div class="mt-1.5 flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3">
            <input :value="initialStock" @input="emit('update:initialStock', $event.target.value)" type="number" min="0" placeholder="0" class="w-full bg-transparent py-2.5 text-xs outline-none" />
            <span class="text-[10px] text-slate-400">un</span>
          </div>
        </label>
        <label class="text-xs font-medium text-slate-600">Lote do Fabricante<input :value="batchCode" @input="emit('update:batchCode', $event.target.value)" placeholder="Ex: LT-2026-01" class="product-input" /></label>
        <label class="text-xs font-medium text-slate-600">Data de Validade<input :value="expiration" @input="emit('update:expiration', $event.target.value)" type="date" class="product-input" /></label>
      </div>
    </section>
  </div>
</template>
