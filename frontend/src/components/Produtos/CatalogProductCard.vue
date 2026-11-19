<script setup>
import { Box, Plus } from '@lucide/vue'
import BaseButton from '../ui/BaseButton.vue'

defineProps({ product: { type: Object, required: true } })
const emit = defineEmits(['add'])
const currency = (value) =>
  Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
</script>

<template>
  <article
    class="flex min-h-[355px] flex-col rounded-2xl border border-slate-200 bg-white p-3 shadow-sm transition hover:shadow-md"
  >
    <div
      class="flex aspect-[4/3] items-center justify-center overflow-hidden rounded-xl bg-slate-100"
    >
      <img
        v-if="product.image_url || product.image"
        :src="product.image_url || product.image"
        :alt="product.name"
        class="h-full w-full object-contain p-3"
      /><Box v-else :size="40" :stroke-width="1.4" class="text-slate-300" />
    </div>
    <div class="flex flex-1 flex-col px-1 pt-3">
      <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">{{
        product.category?.name || 'Geral'
      }}</span>
      <h2 class="mt-1 line-clamp-1 text-sm font-semibold text-slate-900" :title="product.name">
        {{ product.name }}
      </h2>
      <p
        v-if="product.description"
        class="mt-1 line-clamp-2 min-h-8 text-[11px] leading-relaxed text-slate-500"
      >
        {{ product.description }}
      </p>
      <div class="mt-auto flex items-end justify-between gap-3 pt-3">
        <strong class="text-base font-bold text-slate-900"
          >{{ currency(product.price) }}
          <span class="text-[11px] font-normal text-slate-500">{{
            product.unit || '/un'
          }}</span></strong
        ><BaseButton
          @click="emit('add', product)"
          class="h-8 w-8 rounded-xl p-0"
          aria-label="Adicionar à sacola"
          ><Plus :size="16"
        /></BaseButton>
      </div>
    </div>
  </article>
</template>
