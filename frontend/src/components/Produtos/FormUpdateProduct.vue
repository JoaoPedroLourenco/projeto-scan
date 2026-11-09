<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { searchProductById, updateProduct } from '../../services/productService.js'

const id_categoria = ref('')
const name_product = ref('')
const barcode = ref('')
const descricao = ref('')
const preco = ref(0)
const min_stock = ref(0)

const route = useRoute()
const router = useRouter()
const loading = ref(false)
const saving = ref(false)
const productLoaded = ref(false)

async function fetchProductInfo() {
  loading.value = true

  try {
    const response = await searchProductById(Number(route.params.id))
    const product = response?.product ?? response

    if (!product) return

    id_categoria.value = product.category_id ?? ''
    name_product.value = product.name ?? ''
    barcode.value = product.barcode ?? ''
    descricao.value = product.description ?? ''
    preco.value = product.price ?? 0
    min_stock.value = product.min_stock ?? 0

    productLoaded.value = true
  } catch (error) {
    console.error('Erro ao carregar produto:', error.response?.data ?? error.message)
  } finally {
    loading.value = false
  }
}

async function updateProductInfo() {
  saving.value = true

  try {
    const productInfo = {
      category_id: Number(id_categoria.value),
      name: name_product.value,
      barcode: barcode.value,
      description: descricao.value,
      price: preco.value,
      min_stock: min_stock.value,
    }

    await updateProduct(route.params.id, productInfo)
    router.push('/produtos')
  } catch (error) {
    console.error('Erro ao atualizar produto:', error.response?.data ?? error.message)
  } finally {
    saving.value = false
  }
}

onMounted(fetchProductInfo)
</script>

<template>
  <p v-if="loading">Carregando...</p>
  <p v-else-if="!productLoaded">Não foi possível carregar o produto.</p>

  <form v-else @submit.prevent="updateProductInfo" class="w-2/4 flex flex-col">
    <label for="id_categoria">Categoria</label>
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

    <button type="submit" :disabled="saving" class="hover:bg-[#2563EB] cursor-pointer">
      {{ saving ? 'Salvando alterações...' : 'Salvar alterações' }}
    </button>
  </form>
</template>
