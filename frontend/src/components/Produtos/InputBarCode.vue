<script setup>
import { ref } from 'vue'
import { lookupBarCode } from '../../services/productService.js'
import { useProduct } from '../../composables/useProduct'

const { definirProduto } = useProduct()

const barcode = ref('')
const produto = ref(null)

async function lookupByBarcode() {
  try {
    const response = await lookupBarCode(barcode.value)

    produto.value = response.data

    console.log(response.data)
    definirProduto(response.data)
  } catch (error) {
    console.error(error.response.data)
  }
}
</script>

<template>
  <form @submit.prevent="lookupByBarcode">
    <label for="barcode">Código de barras</label>
    <input type="text" name="barcode" id="barcode" v-model="barcode" />

    <button type="submit">Enviar</button>
  </form>

  <div v-if="produto">
    {{ produto.name }}
  </div>
</template>
