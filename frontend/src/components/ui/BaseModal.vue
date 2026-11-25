<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  maxWidth: { type: String, default: 'max-w-md' },
  closeOnOverlay: { type: Boolean, default: true },
})

const emit = defineEmits(['update:modelValue', 'close'])

function close() {
  emit('update:modelValue', false)
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-[2px]"
      @click.self="closeOnOverlay && close()"
    >
      <section
        :class="['w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-xl', maxWidth]"
        role="dialog"
        aria-modal="true"
      >
        <slot />
      </section>
    </div>
  </Teleport>
</template>
