<script setup>
import { onMounted, ref } from 'vue'
import { showAllProducts } from '../../services/productService.js'

const produtos = ref([])
const loading = ref(false)
const erro = ref('')

async function loadProducts() {
  loading.value = true

  try {
    const response = await showAllProducts()

    produtos.value = response.data

    console.log(produtos.value)
  } catch (error) {
    erro.value = error.response.data
    console.error(error.response)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadProducts()
})
</script>

<template>
  <section>
    <p v-if="loading">Carregando...</p>

    <p v-else-if="erro">{{ erro }}</p>

    <p v-else-if="produtos.length === 0">Nenhum produto encontrado.</p>

    <table v-else>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Código de barras</th>
          <th>Preço</th>
          <th>Descrição</th>
          <th>Estoque mínimo</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="produto in produtos" :key="produto.id">
          <td>{{ produto.id }}</td>
          <td>{{ produto.name }}</td>
          <td>{{ produto.barcode }}</td>
          <td>R$ {{ produto.price }}</td>
          <td>{{ produto.description }}</td>
          <td>{{ produto.min_stock }}</td>
          <td>{{ produto.is_active ? 'Ativo' : 'Inativo' }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>
