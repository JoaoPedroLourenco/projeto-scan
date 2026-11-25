<script setup>
import { computed } from 'vue'
import { Camera } from '@lucide/vue'

const props = defineProps({
  form: { type: Object, required: true },
  allergens: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:name', 'update:brand', 'update:category', 'update:description', 'update:barcode'])

const categoryOptions = computed(() => {
  const list = [...props.categories]
  if (props.form.category_id && !list.some((cat) => String(cat.id) === String(props.form.category_id))) {
    list.push({ id: props.form.category_id, name: props.form.category_name || `Categoria #${props.form.category_id}` })
  }
  return list
})

const allergenText = (item) => (typeof item === 'object' && item !== null ? item.name ?? '' : `Alérgeno #${item}`)
</script>

<template>
  <section class="h-fit self-start rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
    <div class="grid gap-4 sm:grid-cols-[144px_1fr]">
      <div class="aspect-square w-full max-w-36 self-start overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-center text-slate-400">
        <span class="flex h-full flex-col items-center justify-center px-2">
          <Camera :size="22" />
          <span class="mt-2 text-xs font-medium text-slate-700">Foto do Produto</span>
        </span>
      </div>
      <div class="grid content-start gap-3 sm:grid-cols-2">
        <label class="text-xs font-medium text-slate-600 sm:col-span-2">
          Nome Completo do Produto
          <input :value="form.name" @input="emit('update:name', $event.target.value)" required maxlength="255" placeholder="Nome do produto (preenchido pela busca ou digite)..." class="product-input" />
        </label>
        <label class="text-xs font-medium text-slate-600 sm:col-span-2">
          Código de Barras (EAN)
          <input :value="form.barcode" @input="emit('update:barcode', $event.target.value)" placeholder="Código de barras" class="product-input" />
        </label>
        <label class="text-xs font-medium text-slate-600">
          Marca / Produtor
          <input :value="form.brand" @input="emit('update:brand', $event.target.value)" placeholder="Ex: Nestlé, Danone" class="product-input" />
        </label>
        <label class="text-xs font-medium text-slate-600">
          Categoria
          <select v-if="categoryOptions.length" :value="form.category_id" @change="emit('update:category', $event.target.value)" required class="product-input">
            <option value="" disabled>Selecione uma categoria</option>
            <option v-for="cat in categoryOptions" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <input v-else :value="form.category_id" @input="emit('update:category', $event.target.value)" inputmode="numeric" placeholder="ID da categoria" required class="product-input" />
        </label>
        <label class="text-xs font-medium text-slate-600 sm:col-span-2">
          Descrição
          <textarea :value="form.description" @input="emit('update:description', $event.target.value)" rows="2" placeholder="Descrição do produto" class="product-input" />
        </label>
      </div>
    </div>
    <div class="mt-5 border-t border-slate-100 pt-4">
      <h2 class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Alérgenos e restrições alimentares</h2>
      <div v-if="allergens.length" class="mt-2 flex flex-wrap gap-2">
        <span v-for="(item, index) in allergens" :key="item.id ?? `${allergenText(item)}-${index}`" class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs text-blue-700">
          {{ allergenText(item) }}
        </span>
      </div>
      <p v-else class="mt-2 text-xs text-slate-400">Nenhum alérgeno informado.</p>
    </div>
  </section>
</template>
