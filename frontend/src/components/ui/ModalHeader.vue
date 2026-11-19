<script setup>
import { X } from '@lucide/vue'

defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
})

const emit = defineEmits(['close'])
</script>

<template>
  <div class="mb-5 flex items-start justify-between gap-4">
    <div>
      <h2 class="text-lg font-semibold text-slate-900">{{ title }}</h2>
      <p v-if="subtitle" class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>
    </div>
    <button
      type="button"
      @click="emit('close')"
      class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
      aria-label="Fechar janela"
    >
      <X :size="18" />
    </button>
  </div>
</template>
