<script setup>
import { ref } from 'vue'
import { registerUser } from '../services/authService.js'
import { useRouter } from 'vue-router'

// variáveis do formulário
const nomeUser = ref('')
const emailUser = ref('')
const cpfUser = ref('')
const telefoneUser = ref('')
const senhaUser = ref('')
const confirmarSenha = ref('')

// variáveis para controle do envio e retorno do formulário
let mensagemReturn = ref('')
const loading = ref(false)

const router = useRouter()

async function register() {
  loading.value = true
  mensagemReturn.value = ''

  try {
    const user = {
      name: nomeUser.value,
      email: emailUser.value,
      cpf: cpfUser.value,
      telefone: telefoneUser.value,
      password: senhaUser.value,
      password_confirmation: confirmarSenha.value,
    }

    const response = await registerUser(user)

    // salva token no sessionStorage para verificação de autenticação.
    sessionStorage.setItem('token', response.data?.token)

    mensagemReturn.value = 'Cadastro realizado com sucesso!'

    // redirecionamento para a página inicial após cadastro com sucesso
    router.push('/home')
  } catch (error) {
    // retorna a mensagem de erro diretamente da API (temporário)
    mensagemReturn.value = error.response?.data.message
    console.log(error.response?.status)
    console.log(error.response?.data)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  Registro

  <div v-if="mensagemReturn != ''">
    {{ mensagemReturn }}
  </div>

  <form @submit.prevent="register">
    <label for="name">
      Nome: *
      <input v-model="nomeUser" type="text" name="name" id="name" maxlength="100" />
    </label>
    <label for="email">
      E-mail: *
      <input
        v-model="emailUser"
        type="email"
        name="email"
        id="email"
        minlength="10"
        maxlength="100"
      />
    </label>
    <label for="cpf">
      CPF: *
      <input v-model="cpfUser" type="text" name="cpf" id="cpf" minlength="14" maxlength="14" />
    </label>
    <label for="telefone">
      Telefone: *
      <input
        v-model="telefoneUser"
        type="tel"
        name="telefone"
        id="telefone"
        minlength="15"
        maxlength="15"
      />
    </label>
    <label for="senha">
      Senha: *
      <input v-model="senhaUser" type="password" name="senha" id="senha" minlength="8" />
    </label>
    <label for="confirmarSenha">
      Confirmar senha: *
      <input
        v-model="confirmarSenha"
        type="password"
        name="confirmarSenha"
        id="confirmarSenha"
        minlength="8"
      />
    </label>

    <button type="submit" :disabled="loading">
      {{ loading ? 'Carregando...' : 'Cadastrar' }}
    </button>
  </form>
</template>
