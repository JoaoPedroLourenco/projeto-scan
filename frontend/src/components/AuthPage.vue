<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import FormLogin from './FormLogin.vue'
import FormRegisterUser from './FormRegisterUser.vue'
import AuthLayout from './ui/AuthLayout.vue'

const props = defineProps({ mode: { type: String, default: 'login' } })
const isRegister = computed(() => props.mode === 'register')
const title = computed(() => (isRegister.value ? 'Comece agora' : 'Bem-vindo de volta'))
const subtitle = computed(() =>
  isRegister.value
    ? 'Preencha seus dados para criar sua conta.'
    : 'Entre com seus dados para acessar sua conta.',
)
</script>

<template>
  <AuthLayout :title="title" :subtitle="subtitle">
    <component :is="isRegister ? FormRegisterUser : FormLogin" />
    <template #footer>
      <p class="mt-5 text-center text-xs text-slate-500">
        {{ isRegister ? 'Já possui uma conta?' : 'Não tem uma conta?' }}
        <RouterLink
          :to="isRegister ? '/login' : '/register'"
          class="font-semibold text-blue-600 hover:underline"
        >
          {{ isRegister ? 'Entrar' : 'Cadastre-se' }}
        </RouterLink>
      </p>
    </template>
  </AuthLayout>
</template>
