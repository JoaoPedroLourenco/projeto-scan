<script setup>
import { ref } from 'vue'
import { userLogin } from '../services/authService.js'
import { useRouter } from 'vue-router'

const router = useRouter()
const emailUser = ref('')
const senhaUser = ref('')
const mensagemReturn = ref('')
const loading = ref(false)

async function login() {
  loading.value = true
  mensagemReturn.value = ''
  try {
    const response = await userLogin({ email: emailUser.value, password: senhaUser.value })
    sessionStorage.setItem('token', response.data?.token)
    router.push('/home')
  } catch (error) {
    mensagemReturn.value =
      error.response?.data?.msg ||
      error.response?.data?.message ||
      'Não foi possível realizar o login. Verifique suas credenciais.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="login" class="w-full space-y-4">
    <div
      v-if="mensagemReturn"
      class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700"
    >
      {{ mensagemReturn }}
    </div>
    <div>
      <label for="email" class="mb-1.5 block text-xs font-medium text-slate-900"
        >E-mail ou CPF</label
      >
      <input
        v-model="emailUser"
        type="text"
        id="email"
        autocomplete="username"
        placeholder="seu.email@exemplo.com"
        required
        class="auth-input"
      />
    </div>
    <div>
      <div class="mb-1.5 flex items-center justify-between">
        <label for="senha" class="block text-xs font-medium text-slate-900">Senha</label
        ><a href="#" class="text-[11px] text-blue-600 hover:underline">Esqueceu a senha?</a>
      </div>
      <input
        v-model="senhaUser"
        type="password"
        id="senha"
        autocomplete="current-password"
        placeholder="••••••••••"
        required
        class="auth-input"
      />
    </div>
    <button
      type="submit"
      :disabled="loading"
      class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.99] disabled:bg-blue-400"
    >
      {{ loading ? 'Entrando...' : 'Entrar na Conta Beepo' }}
      <span v-if="!loading" aria-hidden="true">→</span>
    </button>
  </form>
</template>
