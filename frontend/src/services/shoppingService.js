const CART_KEY = 'beepo_consumer_cart'
const LIST_KEY = 'beepo_consumer_shopping_list'

function lerLista(chave) {
  try {
    return JSON.parse(localStorage.getItem(chave) || '[]')
  } catch {
    return []
  }
}

function salvarLista(chave, evento, itens) {
  localStorage.setItem(chave, JSON.stringify(itens))
  window.dispatchEvent(new Event(evento))
  return itens
}

// --- SACOLA ---
export function getCartItems() {
  return lerLista(CART_KEY)
}

export function addToCart(product, quantidade = 1) {
  const itens = lerLista(CART_KEY)
  const existente = itens.find((item) => item.product_id === product.id)

  if (existente) {
    existente.quantity += quantidade
  } else {
    itens.push({
      id: product.id,
      product_id: product.id,
      quantity: quantidade,
      unit_price: Number(product.price || 0),
      product,
    })
  }
  return salvarLista(CART_KEY, 'cart-updated', itens)
}

export function updateCartQuantity(productId, novaQtd) {
  let itens = lerLista(CART_KEY)
  if (novaQtd <= 0) {
    itens = itens.filter((i) => i.product_id !== productId)
  } else {
    const item = itens.find((i) => i.product_id === productId)
    if (item) item.quantity = novaQtd
  }
  return salvarLista(CART_KEY, 'cart-updated', itens)
}

export function removeCartItem(productId) {
  const itens = lerLista(CART_KEY).filter((i) => i.product_id !== productId)
  return salvarLista(CART_KEY, 'cart-updated', itens)
}

export function clearCart() {
  return salvarLista(CART_KEY, 'cart-updated', [])
}

// --- LISTA DE COMPRAS ---
export function getListItems() {
  return lerLista(LIST_KEY)
}

export function addToList(product, quantidade = 1) {
  const itens = lerLista(LIST_KEY)
  const existente = itens.find((item) => item.product_id === product.id)

  if (existente) {
    existente.quantity += quantidade
  } else {
    itens.push({
      id: product.id,
      product_id: product.id,
      quantity: quantidade,
      is_checked: false,
      product,
    })
  }
  return salvarLista(LIST_KEY, 'list-updated', itens)
}

export function toggleListItem(productId) {
  const itens = lerLista(LIST_KEY)
  const item = itens.find((i) => i.product_id === productId)
  if (item) item.is_checked = !item.is_checked
  return salvarLista(LIST_KEY, 'list-updated', itens)
}

export function updateListQuantity(productId, novaQtd) {
  let itens = lerLista(LIST_KEY)
  if (novaQtd <= 0) {
    itens = itens.filter((i) => i.product_id !== productId)
  } else {
    const item = itens.find((i) => i.product_id === productId)
    if (item) item.quantity = novaQtd
  }
  return salvarLista(LIST_KEY, 'list-updated', itens)
}

export function removeListItem(productId) {
  const itens = lerLista(LIST_KEY).filter((i) => i.product_id !== productId)
  return salvarLista(LIST_KEY, 'list-updated', itens)
}

export function importListToCart() {
  const lista = lerLista(LIST_KEY)
  const restantes = []
  let enviados = 0

  for (const item of lista) {
    if (item.is_checked && item.product) {
      addToCart(item.product, item.quantity)
      enviados++
    } else {
      restantes.push(item)
    }
  }

  salvarLista(LIST_KEY, 'list-updated', restantes)
  return enviados
}
