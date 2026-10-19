<script setup>
import { ref } from 'vue'
import { userLogin } from '../services/authService.js'
import { RouterLink, useRouter } from 'vue-router'

const router = useRouter()

const emailUser = ref('')
const senhaUser = ref('')

let mensagemReturn = ref('')
const loading = ref(false)

async function login() {
  loading.value = true
  mensagemReturn.value = ''

  try {
    const userInfo = {
      email: emailUser.value,
      password: senhaUser.value,
    }

    const response = await userLogin(userInfo)

    // adiciona token na sessionStorage
    sessionStorage.setItem('token', response.data?.token)

    mensagemReturn.value = 'Login realizado com sucesso!'
    console.log(mensagemReturn.value)

    router.push('/home')
  } catch (error) {
    mensagemReturn.value = error.response?.data.msg || error.response?.data.message
    console.log(error.response)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div v-if="mensagemReturn != ''">
    {{ mensagemReturn }}
  </div>

  <form @submit.prevent="login">
    <label for="email">E-mail *</label>
    <input v-model="emailUser" type="email" id="email" />

    <label for="senha">Senha *</label>
    <input v-model="senhaUser" type="password" id="senha" />

    <button type="submit" :disabled="loading">
      {{ loading ? 'Carregando...' : 'Acessar' }}
    </button>
  </form>

  <!-- Permite redirecionamento para a página de cadastro -->
  <RouterLink to="/register" class="bg-[#2563EB]">Cadastro</RouterLink>
</template>
