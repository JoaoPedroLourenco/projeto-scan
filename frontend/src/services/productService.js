import api from './api'

export async function lookupBarCode(barcode) {
  try {
    const response = await api.get(`/products/lookup/${barcode}`)

    return response.data
  } catch (error) {
    console.error('Erro ao consultar código:', error.response?.data ?? error.message)
    throw error
  }
}

export async function createProducts(productInfo) {
  try {
    const response = await api.post('/products', productInfo)
    return response.data
  } catch (error) {
    console.error('Erro ao cadastrar produto:', error.response?.data ?? error.message)
    throw error
  }
}

export async function showAllProducts() {
  try {
    const response = await api.get('/products')

    return response.data
  } catch (error) {
    console.error('Erro ao listar produtos:', error.response?.data ?? error.message)
    throw error
  }
}
