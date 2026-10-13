import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

// Antes de cada requisição, verifica se existe um token e adiciona ao Bearer
api.interceptors.request.use((config) => {
  // token salvo no sessionStorage (temporário)
  const token = sessionStorage.getItem('token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

export default api
