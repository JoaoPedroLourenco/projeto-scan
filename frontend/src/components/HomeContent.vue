<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { ArrowRight, Box, ListChecks, ShoppingBag } from '@lucide/vue'
import CatalogProductCard from './Produtos/CatalogProductCard.vue'
import PageContainer from './ui/PageContainer.vue'

const featuredProducts = ref([])
const selectedItems = ref([])
const selectedTotal = computed(() =>
  selectedItems.value.reduce((total, product) => total + Number(product.price || 0), 0),
)
</script>

<template>
  <PageContainer>
    <header class="mb-7">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Início</h1>
      <p class="mt-1 text-xs text-slate-500">Acompanhe seus produtos e explore o catálogo.</p>
    </header>

    <section
      class="mb-3 flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-3.5"
    >
      <span class="flex min-w-0 items-center gap-4">
        <span
          ><strong class="block text-sm font-semibold text-slate-900">Escanear Produto</strong
          ><span class="mt-1 block text-xs text-slate-500"
            >Aponte o código de barras ou digite o código numérico no teclado.</span
          ></span
        >
      </span>
      <RouterLink
        to="/scanner"
        class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700"
        >Abrir Scanner</RouterLink
      >
    </section>

    <div class="mb-10 grid gap-3 lg:grid-cols-[1.1fr_1fr]">
      <section class="rounded-xl border border-slate-200 bg-white p-4">
        <header class="mb-3 flex items-center justify-between">
          <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
            <ShoppingBag :size="16" /> Minha Sacola Ativa
          </h2>
          <span class="text-[11px] text-slate-500">{{ selectedItems.length }} itens</span>
        </header>
        <div v-if="selectedItems.length" class="mb-3 flex gap-2">
          <span
            v-for="(item, index) in selectedItems.slice(0, 3)"
            :key="index"
            class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-slate-400"
            ><Box :size="14" /></span
          ><span
            v-if="selectedItems.length > 3"
            class="flex h-8 min-w-8 items-center justify-center rounded-md bg-slate-100 px-2 text-[10px] text-slate-600"
            >+{{ selectedItems.length - 3 }}</span
          >
        </div>
        <div class="flex justify-between text-[11px] text-slate-500">
          <span>Subtotal</span
          ><span>{{
            selectedTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
          }}</span>
        </div>
        <div class="mt-2 flex items-center justify-between">
          <strong class="text-xs text-slate-900">Total</strong
          ><strong class="text-lg font-semibold text-slate-900">{{
            selectedTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
          }}</strong>
        </div>
        <button
          type="button"
          class="mt-2 w-full cursor-pointer rounded-lg bg-slate-100 py-2 text-center text-xs font-medium text-slate-700 transition hover:bg-slate-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400"
        >
          Ver Sacola <ArrowRight class="ml-1 inline" :size="14" />
        </button>
      </section>

      <section class="rounded-xl border border-slate-200 bg-white p-4">
        <header class="mb-4 flex items-center justify-between">
          <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-900">
            <ListChecks :size="16" /> Lista de Compras
          </h2>
          <span class="text-[11px] text-slate-500">0 itens salvos</span>
        </header>
        <button
          type="button"
          class="mt-3 w-full cursor-pointer rounded-lg bg-slate-100 py-2 text-center text-xs font-medium text-slate-700 transition hover:bg-slate-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-400"
        >
          Continuar Lista <ArrowRight class="ml-1 inline" :size="14" />
        </button>
      </section>
    </div>

    <section>
      <header class="mb-3 flex items-end justify-between">
        <div>
          <h2 class="text-sm font-semibold text-slate-900">Produtos em destaque</h2>
          <p class="mt-1 text-[11px] text-slate-500">Produtos selecionados para você.</p>
        </div>
        <RouterLink
          to="/catalogo"
          class="text-[11px] font-medium text-blue-600 hover:text-blue-700"
          >Ver catálogo <ArrowRight class="ml-1 inline" :size="13"
        /></RouterLink>
      </header>
      <div v-if="featuredProducts.length" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <CatalogProductCard
          v-for="product in featuredProducts"
          :key="product.id"
          :product="product"
        />
      </div>
    </section>
  </PageContainer>
</template>
