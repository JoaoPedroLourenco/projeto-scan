<script setup>
import { createProducts } from '../services/productservice.js'
import { ref } from 'vue'

// variáveis do formulário
const id_categoria = ref('')
const name_product = ref('')
const barcode = ref('')
const descricao = ref('')
const preco = ref(0)
const min_stock = ref(0)

async function registerProduct() {
  try {
    const newProduct = {
      category_id: Number(id_categoria.value),
      name: name_product.value,
      barcode: barcode.value,
      description: descricao.value,
      price: preco.value,
      min_stock: min_stock.value,
    }

    const produto = await createProducts(newProduct)

    console.log('Produto cadastrado:', produto)
  } catch (error) {
    console.error('Erro ao cadastrar produto:', error.response?.data ?? error.message)
  }
}
</script>

<template>
  <h1>Teste</h1>

  <form @submit.prevent="registerProduct" class="w-2/4 flex flex-col">
    <label for="id_categoria">Id categoria</label>
    <input type="text" id="id_categoria" name="id_categoria" v-model="id_categoria" />

    <label for="name_product">Nome do produto</label>
    <input type="text" id="name_product" name="name_product" v-model="name_product" />

    <label for="barcode">Código de Barras (EAN/GTIN/UPC)</label>
    <input type="text" id="barcode" name="barcode" v-model="barcode" />

    <label for="descricao">Descrição</label>
    <input type="text" id="descricao" name="descricao" v-model="descricao" />

    <label for="preco">Preço</label>
    <input type="number" id="preco" name="preco" v-model.number="preco" />

    <label for="min_stock">Estoque Mínimo</label>
    <input type="number" id="min_stock" name="min_stock" v-model.number="min_stock" />

    <button type="submit">Cadastrar</button>
  </form>
</template>
