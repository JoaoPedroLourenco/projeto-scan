import { ref } from 'vue'

// Shared product state used to populate the product form after a lookup.
const produto = ref(null)

export function useProduct() {
  function definirProduto(dados) {
    produto.value = dados
  }

  return { produto, definirProduto }
}
