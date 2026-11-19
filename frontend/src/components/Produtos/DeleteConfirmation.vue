<script setup>
defineProps({
  productName: { type: String, required: true },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
    @click.self="emit('cancel')"
  >
    <section
      role="dialog"
      aria-modal="true"
      aria-labelledby="delete-title"
      class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
    >
      <h2 id="delete-title" class="text-lg font-semibold text-slate-900">Excluir produto?</h2>
      <p class="my-5 text-sm text-slate-600">
        Tem certeza que deseja excluir
        <strong class="font-semibold text-slate-900">{{ productName }}</strong
        >?
      </p>
      <div class="flex justify-end gap-2">
        <button
          type="button"
          :disabled="loading"
          @click="emit('cancel')"
          class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50"
        >
          Cancelar
        </button>
        <button
          type="button"
          :disabled="loading"
          @click="emit('confirm')"
          class="rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-rose-700 disabled:opacity-50"
        >
          {{ loading ? 'Excluindo...' : 'Excluir produto' }}
        </button>
      </div>
    </section>
  </div>
</template>
