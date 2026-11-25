<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, required: true },
  maxWidth: { type: String, default: 'max-w-[420px]' },
})
</script>

<template>
  <main class="flex min-h-screen items-center justify-center bg-white px-4 py-8 text-slate-900">
    <section
      :class="[
        'my-auto flex w-full flex-col rounded-3xl border border-slate-200 bg-white p-7 shadow-sm sm:p-8',
        maxWidth,
      ]"
    >
      <RouterLink to="/login" class="mb-6 inline-flex" aria-label="Beepo início">
        <img src="@/assets/logo.svg" alt="beepo." class="h-7 w-auto" />
      </RouterLink>
      <header class="mb-6 text-center">
        <h1 class="text-2xl font-bold leading-tight tracking-tight text-slate-900">{{ title }}</h1>
        <p class="mx-auto mt-2 max-w-sm text-xs leading-relaxed text-slate-500">{{ subtitle }}</p>
      </header>
      <slot />
      <slot name="footer" />
    </section>
  </main>
</template>
