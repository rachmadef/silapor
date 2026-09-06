import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { requiresGuest: true },
  },
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/DashboardView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/pickup',
    name: 'pickup',
    component: () => import('@/views/pickup/PickupScannerView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/return',
    name: 'return',
    component: () => import('@/views/return/ReturnScannerView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/monitoring',
    name: 'monitoring',
    component: () => import('@/views/monitoring/MonitoringView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/students',
    name: 'students',
    component: () => import('@/views/students/StudentListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/classes',
    name: 'classes',
    component: () => import('@/views/classes/ClassListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/parents',
    name: 'parents',
    component: () => import('@/views/parents/ParentListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/laptops',
    name: 'laptops',
    component: () => import('@/views/laptops/LaptopListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/schedules',
    name: 'schedules',
    component: () => import('@/views/schedules/ScheduleListView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/notifications',
    name: 'notifications',
    component: () => import('@/views/notifications/NotificationLogView.vue'),
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Navigation Guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
