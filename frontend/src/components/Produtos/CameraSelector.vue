<script setup>
defineProps({
  cameras: { type: Array, default: () => [] },
  modelValue: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'open'])
</script>

<template>
  <select
    :value="modelValue"
    :disabled="disabled"
    aria-label="Selecionar câmera"
    class="max-w-56 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 outline-none focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-400"
    @focus="emit('open')"
    @change="emit('update:modelValue', $event.target.value)"
  >
    <option value="" disabled>
      {{ loading ? 'Buscando câmeras...' : 'Selecionar câmera' }}
    </option>
    <option v-for="(camera, index) in cameras" :key="camera.id" :value="camera.id">
      {{ camera.label || `Câmera ${index + 1}` }}
    </option>
  </select>
</template>
