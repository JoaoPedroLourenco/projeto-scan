import { createRouter, createWebHistory } from 'vue-router'
import { getCurrentUser } from '@/services/authService.js'
import LoginPageView from '@/views/LoginPageView.vue'
import RegisterPageView from '@/views/RegisterPageView.vue'
import HomePageView from '@/views/HomePageView.vue'
import CreateProductView from '@/views/CreateProductView.vue'
import ProductsListView from '@/views/ProductsListView.vue'
import EditProductView from '@/views/EditProductView.vue'
import CatalogView from '@/views/CatalogView.vue'
import ScannerView from '@/views/ScannerView.vue'
import ProfileView from '@/views/ProfileView.vue'
import ShoppingBagView from '@/views/ShoppingBagView.vue'
import ShoppingListView from '@/views/ShoppingListView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/register',
      component: RegisterPageView,
      meta: { guestOnly: true },
    },
    {
      path: '/home',
      component: HomePageView,
      meta: { requiresAuth: true },
    },
    {
      path: '/catalogo',
      component: CatalogView,
      meta: { requiresAuth: true },
    },
    {
      path: '/scanner',
      component: ScannerView,
      meta: { requiresAuth: true },
    },
    {
      path: '/sacola',
      component: ShoppingBagView,
      meta: { requiresAuth: true },
    },
    {
      path: '/lista',
      component: ShoppingListView,
      meta: { requiresAuth: true },
    },
    {
      path: '/perfil',
      component: ProfileView,
      meta: { requiresAuth: true },
    },
    {
      path: '/login',
      component: LoginPageView,
      meta: { guestOnly: true },
    },
    {
      path: '/produtos',
      component: ProductsListView,
      meta: { requiresAuth: true, roles: ['admin', 'manager'] },
    },
    {
      path: '/produtos/registro',
      component: CreateProductView,
      meta: { requiresAuth: true, roles: ['admin', 'manager'] },
    },
    {
      path: '/produtos/editar/:id',
      component: EditProductView,
      meta: { requiresAuth: true, roles: ['admin', 'manager'] },
    },
  ],
})

router.beforeEach(async (to) => {
  const token = sessionStorage.getItem('token')
  const isAuthenticated = Boolean(token) // se existir token é true

  // usuário não autenticado é redirecionado para página de login
  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/login'
  }

  // usuário já logado tentando acessar a página de login, é encaminhado para a home.
  if (to.meta.guestOnly && isAuthenticated) {
    return '/home'
  }

  // bloqueia rotas de gerência para clientes
  const allowedRoles = to.meta.roles as string[] | undefined
  if (allowedRoles?.length) {
    try {
      let role = sessionStorage.getItem('user_role')
      if (!role) {
        const user = await getCurrentUser()
        role = user?.role || ''
      }
      if (!allowedRoles.includes(role || '')) {
        return '/home'
      }
    } catch {
      return '/login'
    }
  }
})

export default router
