import AdminLayout from '@/layouts/AdminLayout.vue'
import Dashboard from '@/pages/admin/dashboard.vue'
import Hrh from '@/pages/admin/hrh/Hrh.vue'
import PublicDashboard from '@/pages/public/Dashboard.vue'
import Login from '@/pages/public/Login.vue'
import { createRouter, createWebHistory } from 'vue-router'


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
          name: 'Dashboard',
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

export default router