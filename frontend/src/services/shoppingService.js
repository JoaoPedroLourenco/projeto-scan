import api from './api'

async function ensureCartId() {
  let cartId = sessionStorage.getItem('active_cart_id')
  if (cartId) return cartId
  const res = await api.post('/cart')
  cartId = res.data?.cart?.id
  if (cartId) sessionStorage.setItem('active_cart_id', String(cartId))
  return cartId
}

async function ensureListId() {
  let listId = sessionStorage.getItem('active_list_id')
  if (listId) return listId
  const res = await api.get('/shopping-lists')
  const lists = res.data?.shoppingLists || []
  if (lists.length) {
    listId = lists[0].id
  } else {
    const created = await api.post('/shopping-lists', { name: 'Minha Lista' })
    listId = created.data?.shoppingList?.id
  }
  if (listId) sessionStorage.setItem('active_list_id', String(listId))
  return listId
}

// --- SACOLA ---
export async function getCartItems() {
  try {
    const cartId = await ensureCartId()
    if (!cartId) return []
    const res = await api.get(`/cart/${cartId}`)
    return res.data?.cart?.items || []
  } catch {
    sessionStorage.removeItem('active_cart_id')
    return []
  }
}

export async function addToCart(product, quantidade = 1) {
  let cartId = await ensureCartId()
  try {
    await api.post(`/cart/${cartId}/items`, { product_id: product.id, quantity: quantidade })
  } catch (err) {
    if (err.response?.status === 422 && err.response?.data?.errors?.allergen) {
      const msg = err.response.data.errors.allergen[0]
      if (!window.confirm(`${msg}\n\nDeseja adicionar mesmo assim?`)) return false
      await api.post(`/cart/${cartId}/items`, { product_id: product.id, quantity: quantidade, ignore_allergens: true })
    } else if (err.response?.status === 422 && err.response?.data?.errors?.cart) {
      sessionStorage.removeItem('active_cart_id')
      cartId = await ensureCartId()
      await api.post(`/cart/${cartId}/items`, { product_id: product.id, quantity: quantidade })
    } else {
      throw err
    }
  }
  window.dispatchEvent(new Event('cart-updated'))
  return true
}

export async function updateCartQuantity(cartItemId, novaQtd) {
  const cartId = await ensureCartId()
  await api.put(`/cart/${cartId}/items/${cartItemId}`, { quantity: novaQtd })
  window.dispatchEvent(new Event('cart-updated'))
  return getCartItems()
}

export async function removeCartItem(cartItemId) {
  const cartId = await ensureCartId()
  await api.delete(`/cart/${cartId}/items/${cartItemId}`)
  window.dispatchEvent(new Event('cart-updated'))
  return getCartItems()
}

export async function checkoutCart() {
  const cartId = await ensureCartId()
  const res = await api.post(`/cart/${cartId}/checkout`)
  sessionStorage.removeItem('active_cart_id')
  window.dispatchEvent(new Event('cart-updated'))
  return res.data
}

// --- LISTA DE COMPRAS ---
export async function getListItems() {
  try {
    const listId = await ensureListId()
    if (!listId) return []
    const res = await api.get(`/shopping-lists/${listId}`)
    return res.data?.shoppingList?.items || []
  } catch {
    sessionStorage.removeItem('active_list_id')
    return []
  }
}

export async function addToList(product, quantidade = 1) {
  const listId = await ensureListId()
  await api.post(`/shopping-lists/${listId}/items`, { product_id: product.id, quantity: quantidade })
  window.dispatchEvent(new Event('list-updated'))
  return getListItems()
}

export async function toggleListItem(item) {
  const listId = await ensureListId()
  await api.put(`/shopping-lists/${listId}/items/${item.id}`, { is_checked: !item.is_checked })
  window.dispatchEvent(new Event('list-updated'))
  return getListItems()
}

export async function updateListQuantity(item, novaQtd) {
  if (novaQtd <= 0) return removeListItem(item.id)
  const listId = await ensureListId()
  await api.put(`/shopping-lists/${listId}/items/${item.id}`, { quantity: novaQtd })
  window.dispatchEvent(new Event('list-updated'))
  return getListItems()
}

export async function removeListItem(itemId) {
  const listId = await ensureListId()
  await api.delete(`/shopping-lists/${listId}/items/${itemId}`)
  window.dispatchEvent(new Event('list-updated'))
  return getListItems()
}

export async function importListToCart() {
  const listId = await ensureListId()
  const list = await getListItems()
  const checked = list.filter((i) => i.is_checked && i.product)
  if (!checked.length) return 0

  const res = await api.post(`/shopping-lists/${listId}/start-cart`)
  if (res.data?.cart?.id) {
    sessionStorage.setItem('active_cart_id', String(res.data.cart.id))
  }

  let enviados = 0
  for (const item of checked) {
    const ok = await addToCart(item.product, item.quantity || 1)
    if (ok) enviados++
  }

  window.dispatchEvent(new Event('cart-updated'))
  window.dispatchEvent(new Event('list-updated'))
  return enviados
}
