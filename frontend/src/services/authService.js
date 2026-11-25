import api from './api'

export const ROLE_LABELS = {
  admin: 'Administrador',
  manager: 'Gerente',
  operator: 'Operador',
  customer: 'Cliente',
}

let currentUserCache = null

export function registerUser(userInfo) {
  currentUserCache = null
  return api.post('/auth/register', userInfo)
}

export async function getCurrentUser() {
  if (currentUserCache) return currentUserCache
  const response = await api.get('/auth/me')
  const user = response.data.user ?? response.data
  currentUserCache = user
  if (user?.role) sessionStorage.setItem('user_role', user.role)
  return user
}

export function userLogin(userInfo) {
  currentUserCache = null
  return api.post('/auth/login', userInfo)
}

export async function getAllergens() {
  const response = await api.get('/allergens')
  return response.data.allergens ?? response.data.data ?? response.data ?? []
}

export async function syncUserAllergens(allergenIds) {
  const response = await api.put('/auth/me/allergens', { allergen_ids: allergenIds })
  currentUserCache = null
  return response
}

export async function logout() {
  try {
    await api.post('/auth/logout')
  } catch (error) {
    console.error(error.message)
  } finally {
    currentUserCache = null
    sessionStorage.removeItem('token')
    sessionStorage.removeItem('user_role')
    sessionStorage.removeItem('active_cart_id')
    sessionStorage.removeItem('active_list_id')
    localStorage.removeItem('beepo-profile-restrictions')
    localStorage.removeItem('beepo_consumer_cart')
    localStorage.removeItem('beepo_consumer_shopping_list')
  }
}
