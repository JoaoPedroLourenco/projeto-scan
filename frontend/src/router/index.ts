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
    },
    {
      path: '/home',
      component: HomePageView,
    },
  ],
})

export default router
