<script setup>
import { ChevronLeft, ChevronRight, Pencil, Trash2 } from '@lucide/vue'
import { formatCurrency } from '../../utils/masks.js'

defineProps({
  products: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  page: { type: Number, default: 1 },
  lastPage: { type: Number, default: 1 },
  total: { type: Number, default: 0 },
})
const emit = defineEmits(['edit', 'delete', 'change-page'])
</script>

<template>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[950px] border-collapse text-left">
        <thead class="bg-slate-50 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
          <tr>
            <th class="px-4 py-3">Produto</th>
            <th class="px-3 py-3">Estoque</th>
            <th class="px-3 py-3">EAN</th>
            <th class="px-3 py-3">Lote</th>
            <th class="px-3 py-3">Validade</th>
            <th class="px-3 py-3">Status</th>
            <th class="px-3 py-3">Preço</th>
            <th class="px-4 py-3 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="loading">
            <td colspan="8" class="px-4 py-12 text-center text-xs text-slate-500">Carregando produtos...</td>
          </tr>
          <tr v-else-if="!products.length">
            <td colspan="8" class="px-4 py-16 text-center text-xs text-slate-400">Nenhum produto encontrado com os filtros atuais.</td>
          </tr>
          <tr v-for="product in products" v-else :key="product.id" class="text-xs text-slate-600 transition hover:bg-slate-50/70">
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <span class="h-9 w-9 shrink-0 rounded-xl bg-slate-100"></span>
                <span>
                  <strong class="block max-w-64 truncate font-semibold text-slate-900">{{ product.name }}</strong>
                  <span class="mt-0.5 block text-[10px] text-slate-400">{{ product.category?.name || 'Sem categoria' }}</span>
                </span>
              </div>
            </td>
            <td class="px-3 py-3">
              {{ product.stock_quantity ?? '—' }}<span v-if="product.stock_quantity != null" class="ml-1 text-[10px] text-slate-400">un</span>
            </td>
            <td class="px-3 py-3 font-mono text-[10px]">{{ product.barcode || '—' }}</td>
            <td class="px-3 py-3 text-[10px]">{{ product.batch_code || '—' }}</td>
            <td class="px-3 py-3 text-[10px]">{{ product.expiration_date || '—' }}</td>
            <td class="px-3 py-3">
              <span :class="product.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'" class="rounded-full px-2.5 py-1 text-[10px] font-medium">
                {{ product.is_active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-3 py-3 font-semibold text-slate-900">{{ formatCurrency(product.price) }}</td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-1">
                <button type="button" @click="emit('edit', product)" class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600" aria-label="Editar produto"><Pencil :size="14" /></button>
                <button type="button" @click="emit('delete', product)" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" aria-label="Excluir produto"><Trash2 :size="14" /></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <footer class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-4 py-3 text-[11px] text-slate-500">
      <span>Exibindo {{ products.length ? (page - 1) * 15 + 1 : 0 }}-{{ (page - 1) * 15 + products.length }} de {{ total }} produtos</span>
      <div class="flex items-center gap-2">
        <button type="button" :disabled="page <= 1 || loading" @click="emit('change-page', page - 1)" class="rounded-lg border border-slate-200 p-1.5 hover:bg-white disabled:opacity-40" aria-label="Página anterior"><ChevronLeft :size="14" /></button>
        <span class="rounded-lg bg-blue-600 px-2.5 py-1.5 font-semibold text-white">{{ page }}</span>
        <button type="button" :disabled="page >= lastPage || loading" @click="emit('change-page', page + 1)" class="rounded-lg border border-slate-200 p-1.5 hover:bg-white disabled:opacity-40" aria-label="Próxima página"><ChevronRight :size="14" /></button>
      </div>
    </footer>
  </div>
</template>
