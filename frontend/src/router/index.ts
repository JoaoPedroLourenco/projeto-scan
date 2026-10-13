import { createRouter, createWebHistory } from 'vue-router'
import LoginPageView from '@/views/LoginPageView.vue'
import RegisterPageView from '@/views/RegisterPageView.vue'
import HomePageView from '@/views/HomePageView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
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
  ],
})

router.beforeEach((to) => {
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
})

export default router
