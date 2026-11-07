<script setup>
defineProps({
  cameras: {
    type: Array,
    default: () => [],
  },
  modelValue: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <select
      name="cameras"
      :value="modelValue"
      @change="emit('update:modelValue', $event.target.value)"
    >
      <option value="" disabled>Selecione uma câmera</option>

      <option v-for="camera in cameras" :key="camera.id" :value="camera.id">
        {{ camera.label }}
      </option>
    </select>
  </div>
</template>
