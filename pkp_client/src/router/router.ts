import AdminLayout from '@/layouts/AdminLayout.vue'
import Dashboard from '@/pages/admin/dashboard.vue'
import Hrh from '@/pages/admin/hrh/Hrh.vue'
import Indicators from '@/pages/admin/hrh/Indicators.vue';
import PublicDashboard from '@/pages/public/Dashboard.vue'
import Login from '@/pages/public/Login.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/authStore'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Login',
      component: Login,
    },
    {
      path: '/dashboard',
      name: 'PUBLIC-DASHBOARD',
      component: PublicDashboard,
    },
    {
      path: '/indicators',
      name: 'Indicators',
      component: Indicators,
    },
    {
      path: '/admin',
      name: 'Admin',
      component: AdminLayout,
      meta: {
        requiresAuth: true,
        breadcrumbs: [
          {
            name: 'Admin',
            link: '/admin'
          },
        ]
      },
      children: [
        {
          path: 'dashboard',
          name: 'AdminDashboard',
          component: Dashboard,
          meta: {
            requiresAuth: true,
            breadcrumbs: [
              {
                name: 'Admin',
                link: '/admin'
              },
              {
                name: 'Dashboard',
                link: '/admin/dashboard'
              }
            ]
          }
        },
        {
          path: 'hrh',
          name: 'HRH',
          component: Hrh,
          meta: {
            requiresAuth: true,
            breadcrumbs: [
              {
                name: 'Admin',
                link: '/admin'
              },
              {
                name: 'HRH Listing',
                link: '/admin/hrh'
              }
            ]
          }
        },
      ]
    }


  ]
})


let isInitialized = true

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    
    if (!isInitialized) {
        await authStore.initAuth()
        isInitialized = true
    }
    
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    
    if (requiresAuth && !authStore.isAuthenticated) {
      next({ name: 'Login' })
    }

    else if (!requiresAuth && authStore.isAuthenticated && to.name === 'Login') {
      next({ name: 'AdminDashboard' })
    } 
    
    else {
      next()
    }
})

export default router