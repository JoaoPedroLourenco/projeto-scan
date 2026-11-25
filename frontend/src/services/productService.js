import api from './api'

let productsCache = null

export async function lookupBarCode(barcode) {
  const response = await api.get(`/products/lookup/${barcode}`)
  return response.data
}

export async function createProducts(productInfo) {
  const response = await api.post('/products', productInfo)
  productsCache = null
  return response.data
}

export async function showAllProducts(forceRefresh = false) {
  if (productsCache && !forceRefresh) return productsCache
  const response = await api.get('/products')
  productsCache = response.data
  return productsCache
}

export async function searchProductById(productId) {
  const response = await api.get(`/products/${productId}`)
  return response.data
}

export async function updateProduct(productId, productInfo) {
  const response = await api.put(`/products/${productId}`, productInfo)
  productsCache = null
  return response.data
}

export async function deleteProduct(productId) {
  const response = await api.delete(`/products/${productId}`)
  productsCache = null
  return response.data
}

export async function getStockBatches() {
  const response = await api.get('/stock')
  return response.data?.data ?? response.data ?? []
}

export async function registerStockEntry(entryData) {
  const response = await api.post('/stock/entry', entryData)
  return response.data
}

// Salva estoque, lote e validade no navegador (localStorage)
const STOCK_KEY = 'beepo_product_stock'

export function getProductStockMap() {
  try {
    return JSON.parse(localStorage.getItem(STOCK_KEY) || '{}')
  } catch {
    return {}
  }
}

export function saveProductStock(productId, stockInfo) {
  if (!productId) return
  const dados = getProductStockMap()
  dados[productId] = {
    stock_quantity: Number(stockInfo.stock_quantity || 0),
    batch_code: stockInfo.batch_code || '',
    expiration_date: stockInfo.expiration_date || '',
  }
  localStorage.setItem(STOCK_KEY, JSON.stringify(dados))
}

export function removeProductStock(productId) {
  const dados = getProductStockMap()
  delete dados[productId]
  localStorage.setItem(STOCK_KEY, JSON.stringify(dados))
}

// Pega as categorias e alérgenos únicos a partir da lista de produtos
export function getCategoriesFromProducts(products, extraCategory = null) {
  const lista = []
  for (const item of products) {
    if (item.category && !lista.some((c) => c.id === item.category.id)) {
      lista.push(item.category)
    }
  }
  if (extraCategory && !lista.some((c) => c.id === extraCategory.id)) {
    lista.push(extraCategory)
  }
  return lista
}

export function getAllergensFromProducts(products) {
  const lista = []
  for (const item of products) {
    for (const alg of item.allergens || []) {
      if (alg && !lista.some((a) => a.id === alg.id)) {
        lista.push(alg)
      }
    }
  }
  return lista
}
