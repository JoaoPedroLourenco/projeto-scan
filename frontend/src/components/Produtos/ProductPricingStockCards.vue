<script setup>
defineProps({
  cost: { type: Number, default: 0 },
  margin: { type: Number, default: 0 },
  suggestedPrice: { type: Number, default: 0 },
  profit: { type: Number, default: 0 },
  initialStock: { type: Number, default: 0 },
  minStock: { type: Number, default: 0 },
  batchCode: { type: String, default: '' },
  expiration: { type: String, default: '' },
})
const emit = defineEmits([
  'update:cost',
  'update:margin',
  'update:initialStock',
  'update:minStock',
  'update:batchCode',
  'update:expiration',
])
</script>

<template>
  <div class="space-y-4">
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
      <h2 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-800">
        Formação de Preços e Margem
      </h2>
      <div class="grid grid-cols-2 gap-3">
        <label class="text-xs font-medium text-slate-600"
          >Preço de Custo (R$)<input
            :value="cost"
            @input="emit('update:cost', Number($event.target.value))"
            type="number"
            min="0"
            step="0.01"
            placeholder="0,00"
            class="product-input"
        /></label>
        <label class="text-xs font-medium text-slate-600"
          >Margem Alvo (%)<input
            :value="margin"
            @input="emit('update:margin', Number($event.target.value))"
            type="number"
            min="0"
            step="1"
            placeholder="0"
            class="product-input"
        /></label>
      </div>
      <div class="mt-3 rounded-xl bg-slate-50 p-3.5">
        <div class="flex justify-between text-[11px] text-slate-500">
          <span>Preço de Venda Sugerido</span
          ><span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] text-emerald-700"
            >+{{
              profit.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            }}
            / un (Lucro Bruto)</span
          >
        </div>
        <strong class="mt-2 block text-2xl font-bold text-blue-600"
          >{{ suggestedPrice.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
          <span class="text-[10px] font-normal text-slate-400">por unidade</span></strong
        >
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
      <h2 class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-800">
        Estoque &amp; Controle de Validade
      </h2>
      <div class="grid grid-cols-2 gap-3">
        <label class="text-xs font-medium text-slate-600"
          >Estoque Inicial
          <div class="mt-1.5 flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3">
            <input
              :value="initialStock"
              @input="emit('update:initialStock', Number($event.target.value))"
              type="number"
              min="0"
              class="w-full bg-transparent py-2.5 text-xs outline-none"
            /><span class="text-[10px] text-slate-400">un</span>
          </div></label
        >
        <label class="text-xs font-medium text-slate-600"
          >Alerta Mínimo
          <div class="mt-1.5 flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3">
            <input
              :value="minStock"
              @input="emit('update:minStock', Number($event.target.value))"
              type="number"
              min="0"
              class="w-full bg-transparent py-2.5 text-xs outline-none"
            /><span class="text-[10px] text-slate-400">un</span>
          </div></label
        >
        <label class="text-xs font-medium text-slate-600"
          >Lote do Fabricante<input
            :value="batchCode"
            @input="emit('update:batchCode', $event.target.value)"
            placeholder="Ex: LT-2026-01"
            class="product-input"
        /></label>
        <label class="text-xs font-medium text-slate-600"
          >Data de Validade<input
            :value="expiration"
            @input="emit('update:expiration', $event.target.value)"
            type="date"
            class="product-input"
        /></label>
      </div>
    </section>
  </div>
</template>
