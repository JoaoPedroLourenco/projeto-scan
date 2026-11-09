<script setup>
defineProps({
  productName: { type: String, required: true },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
  <div
    class="fixed inset-0 flex items-center justify-center bg-black/50"
    @click.self="emit('cancel')"
  >
    <section
      class="bg-white p-4"
      role="dialog"
      aria-modal="true"
      aria-labelledby="delete-confirmation-title"
    >
      <h2 id="delete-confirmation-title">Excluir produto?</h2>
      <p>Tem certeza de que deseja excluir {{ productName }}?</p>

      <div>
        <button type="button" :disabled="loading" @click="emit('cancel')">
          Cancelar
        </button>
        <button type="button" :disabled="loading" @click="emit('confirm')">
          {{ loading ? 'Excluindo...' : 'Excluir' }}
        </button>
      </div>
    </section>
  </div>
</template>
