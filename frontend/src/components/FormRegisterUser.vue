<script setup>
import { ref } from 'vue'
import { registerUser } from '../services/authService.js'
import { useRouter } from 'vue-router'
import { formatCpf, formatPhone } from '../utils/masks.js'

const router = useRouter()
const nomeUser = ref('')
const emailUser = ref('')
const cpfUser = ref('')
const telefoneUser = ref('')
const senhaUser = ref('')
const confirmarSenha = ref('')
const mensagemReturn = ref('')
const loading = ref(false)

async function register() {
  mensagemReturn.value = ''
  if (senhaUser.value !== confirmarSenha.value) {
    mensagemReturn.value = 'As senhas não coincidem.'
    return
  }

  loading.value = true
  try {
    const response = await registerUser({
      name: nomeUser.value,
      email: emailUser.value,
      cpf: cpfUser.value.replace(/\D/g, ''),
      phone: telefoneUser.value.replace(/\D/g, ''),
      password: senhaUser.value,
      password_confirmation: confirmarSenha.value,
    })
    sessionStorage.setItem('token', response.data?.token)
    router.push('/home')
  } catch (error) {
    const backendErrors = error.response?.data?.errors
    mensagemReturn.value =
      (backendErrors && Object.values(backendErrors).flat()[0]) ||
      error.response?.data?.message ||
      'Não foi possível concluir o cadastro. Verifique os dados.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="register" class="w-full space-y-3">
    <div v-if="mensagemReturn" class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
      {{ mensagemReturn }}
    </div>
    <div>
      <label for="name" class="mb-1.5 block text-xs font-medium text-slate-900">Nome Completo</label>
      <input v-model="nomeUser" id="name" name="name" maxlength="100" placeholder="Ex: João da Silva" required class="auth-input" />
    </div>
    <div>
      <label for="email" class="mb-1.5 block text-xs font-medium text-slate-900">E-mail</label>
      <input v-model="emailUser" type="email" id="email" name="email" minlength="10" maxlength="100" placeholder="seu.email@exemplo.com" required class="auth-input" />
    </div>
    <div>
      <label for="cpf" class="mb-1.5 block text-xs font-medium text-slate-900">CPF (para cupom fiscal e identificação rápida)</label>
      <input id="cpf" :value="cpfUser" @input="cpfUser = formatCpf($event.target.value)" placeholder="000.000.000-00" inputmode="numeric" minlength="14" maxlength="14" required class="auth-input" />
    </div>
    <div>
      <label for="telefone" class="mb-1.5 block text-xs font-medium text-slate-900">Telefone (opcional)</label>
      <input id="telefone" :value="telefoneUser" @input="telefoneUser = formatPhone($event.target.value)" placeholder="(11) 99000-9999" inputmode="numeric" maxlength="15" class="auth-input" />
    </div>
    <div>
      <div class="mb-1.5 flex items-center justify-between">
        <label for="senha" class="text-xs font-medium text-slate-900">Senha</label>
        <span class="text-[10px] font-medium text-emerald-600">Mínimo: 8 dígitos</span>
      </div>
      <input v-model="senhaUser" type="password" id="senha" name="senha" minlength="8" placeholder="Crie sua senha segura" required class="auth-input" />
    </div>
    <div>
      <label for="confirmarSenha" class="mb-1.5 block text-xs font-medium text-slate-900">Confirmar senha</label>
      <input v-model="confirmarSenha" type="password" id="confirmarSenha" name="confirmarSenha" minlength="8" placeholder="Digite a senha novamente" required class="auth-input" />
    </div>
    <button type="submit" :disabled="loading" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:bg-blue-400">
      {{ loading ? 'Cadastrando...' : 'Criar Conta Beepo' }}
      <span v-if="!loading" aria-hidden="true">→</span>
    </button>
  </form>
</template>
