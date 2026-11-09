<script setup>
import { onMounted, ref } from 'vue'
import { deleteProduct, showAllProducts } from '../../services/productService.js'
import { useRouter } from 'vue-router'
import DeleteConfirmation from './DeleteConfirmation.vue'

const produtos = ref([])
const loading = ref(false)
const erro = ref('')
const produtoSelecionado = ref(null)
const excluindo = ref(false)

const router = useRouter()

async function loadProducts() {
  loading.value = true

  try {
    const response = await showAllProducts()

    produtos.value = response.data ?? []
  } catch (error) {
    erro.value = error.response?.data?.message ?? 'Não foi possível carregar os produtos.'
    console.error(error.response)
  } finally {
    loading.value = false
  }
}

function goToEditPage(produtoId) {
  router.push(`/produtos/editar/${produtoId}`)
}

async function confirmDelete() {
  if (!produtoSelecionado.value || excluindo.value) return

  excluindo.value = true
  erro.value = ''

  try {
    await deleteProduct(produtoSelecionado.value.id)
    produtos.value = produtos.value.filter((produto) => produto.id !== produtoSelecionado.value.id)
    produtoSelecionado.value = null
  } catch (error) {
    erro.value = error.response?.data?.message ?? 'Não foi possível excluir o produto.'
    console.error(error.response ?? error)
  } finally {
    excluindo.value = false
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
          <th>Categoria</th>
          <th>Código de barras</th>
          <th>Preço</th>
          <th>Descrição</th>
          <th>Estoque mínimo</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="produto in produtos" :key="produto.id">
          <td>{{ produto.id }}</td>
          <td>{{ produto.name }}</td>
          <td>{{ produto.category.name }}</td>
          <td>{{ produto.barcode }}</td>
          <td>R$ {{ produto.price }}</td>
          <td>{{ produto.description }}</td>
          <td>{{ produto.min_stock }}</td>
          <td>{{ produto.is_active ? 'Ativo' : 'Inativo' }}</td>
          <td>
            <div>
              <button @click="goToEditPage(produto.id)">Editar</button>
              <button type="button" @click="produtoSelecionado = produto">Excluir</button>
            </div>
          </td>
        </tr>
      </tbody>

      <DeleteConfirmation
        v-if="produtoSelecionado"
        :product-name="produtoSelecionado.name"
        :loading="excluindo"
        @confirm="confirmDelete"
        @cancel="produtoSelecionado = null"
      />
    </table>

    <p v-if="erro && !loading">{{ erro }}</p>
  </section>
</template>

<style scoped>
td {
  padding: 8px;
  border: 1px solid black;
}
</style>
