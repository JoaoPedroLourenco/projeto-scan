import { createRouter, createWebHistory } from 'vue-router'
import LoginPageView from '@/views/LoginPageView.vue'
import RegisterPageView from '@/views/RegisterPageView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: RegisterPageView,
    },
  ],
})

export default router
