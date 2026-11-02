import api from './api'

export async function createProducts(productInfo) {
  try {
    const response = await api.post('/products', productInfo)
    return response.data
  } catch (error) {
    console.error('Erro ao cadastrar produto:', error.response?.data ?? error.message)
    throw error
  }
}
