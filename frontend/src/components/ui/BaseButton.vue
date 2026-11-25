<script setup>
import { computed, useAttrs } from 'vue'

defineOptions({ inheritAttrs: false })

const props = defineProps({
  variant: { type: String, default: 'primary' },
  type: { type: String, default: 'button' },
})
const attrs = useAttrs()

const variants = {
  primary: 'bg-blue-600 text-white hover:bg-blue-700',
  outline: 'border border-slate-200 bg-white text-blue-600 hover:bg-blue-50',
  'outline-neutral': 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
  secondary: 'bg-slate-100 text-slate-700 hover:bg-slate-200',
  ghost: 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
}
const variantClass = computed(() => variants[props.variant] || variants.primary)
</script>

<template>
  <button
    v-bind="attrs"
    :type="type"
    :class="[
      [
        'inline-flex items-center justify-center gap-2 transition',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600',
        'disabled:cursor-default disabled:opacity-100',
      ],
      variantClass,
    ]"
  >
    <slot />
  </button>
</template>
