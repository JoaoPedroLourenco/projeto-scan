<script setup>
import { ref } from 'vue'
import { registerUser } from '../services/authService.js'
import { useRouter } from 'vue-router'
import { formatCpf, formatPhone } from '../utils/masks.js'

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

// Máscaras para os inputs de telefone e CPF
function handleCpfInput(event) {
  cpfUser.value = formatCpf(event.target.value)
}

function handlePhoneInput(event) {
  telefoneUser.value = formatPhone(event.target.value)
}

const router = useRouter()

async function register() {
  loading.value = true
  mensagemReturn.value = ''

  try {
    const user = {
      name: nomeUser.value,
      email: emailUser.value,
      cpf: cpfUser.value.replace(/\D/g, ''),
      telefone: telefoneUser.value.replace(/\D/g, ''),
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
    <label for="name"> Nome: * </label>
    <input v-model="nomeUser" type="text" name="name" id="name" maxlength="100" />
    <label for="email"> </label>
    E-mail: *
    <input
      v-model="emailUser"
      type="email"
      name="email"
      id="email"
      minlength="10"
      maxlength="100"
      placeholder="exemplo@email.com"
    />
    <label for="cpf">CPF:</label>
    <input
      id="cpf"
      :value="cpfUser"
      @input="handleCpfInput"
      placeholder="000.000.000-00"
      inputmode="numeric"
      minlength="14"
      maxlength="14"
    />

    <label for="telefone">Telefone:</label>
    <input
      id="telefone"
      :value="telefoneUser"
      @input="handlePhoneInput"
      placeholder="(11) 99000-9999"
      inputmode="numeric"
      minlength="15"
      maxlength="15"
    />
    <label for="senha"> Senha: * </label>
    <input
      v-model="senhaUser"
      type="password"
      name="senha"
      id="senha"
      minlength="8"
      placeholder="***********"
    />
    <label for="confirmarSenha"> Confirmar senha: * </label>
    <input
      v-model="confirmarSenha"
      type="password"
      name="confirmarSenha"
      id="confirmarSenha"
      minlength="8"
      placeholder="***********"
    />

    <button type="submit" :disabled="loading">
      {{ loading ? 'Carregando...' : 'Cadastrar' }}
    </button>
  </form>
</template>
