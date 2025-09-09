import AdminLayout from '@/layouts/AdminLayout.vue'
import Dashboard from '@/pages/admin/dashboard.vue'
import Hrh from '@/pages/admin/hrh/Hrh.vue'
import PublicDashboard from '@/pages/public/PublicDashboard.vue'
import Login from '@/pages/public/Login.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/authStore'
import Programs from '@/pages/admin/programs/Programs.vue';
import Indicators from '@/pages/admin/indicators/Indicators.vue'
import PlaceSelection from '@/components/PlaceSelection.vue'
import Teams from '@/pages/admin/team/Teams.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Login',
      component: Login,
    },
    {
      path: '/place',
      name: 'place',
      component: PlaceSelection,
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
          path: 'programs',
          name: 'Programs',
          component: Programs,
          meta: {
            requiresAuth: true,
            breadcrumbs: [
              {
                name: 'Admin',
                link: '/admin'
              },
              {
                name: 'Programs',
                link: '/admin/programs'
              }
            ]
          }
        },
        // {
        //   path: 'indicators',
        //   name: 'indicators',
        //   component: Indicators,
        //   meta: {
        //     requiresAuth: true,
        //     breadcrumbs: [
        //       {
        //         name: 'Admin',
        //         link: '/admin'
        //       },
        //       {
        //         name: 'Indicators',
        //         link: '/admin/indicators'
        //       }
        //     ]
        //   }
        // },
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
        {
          path: 'teams',
          name: 'Team',
          component: Teams,
          meta: {
            requiresAuth: true,
            breadcrumbs: [
              {
                name: 'Admin',
                link: '/admin'
              },
              {
                name: 'Teams',
                link: '/admin/teams'
              }
            ]
          }
        },
      ]
    }


  ]
})


let isInitialized = false;

router.beforeEach(async (to, _from, next) => {
  try {
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
  } catch (error) {
    console.error('Navigation guard error:', error)
    next({ name: 'Login' }) // Fallback to login on error
  }

})

export default router