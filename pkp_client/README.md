# Vue 3 + TypeScript + Vite

This template should help get you started developing with Vue 3 and TypeScript in Vite. The template uses Vue 3 `<script setup>` SFCs, check out the [script setup docs](https://v3.vuejs.org/api/sfc-script-setup.html#sfc-script-setup) to learn more.

Learn more about the recommended Project Setup and IDE Support in the [Vue Docs TypeScript Guide](https://vuejs.org/guide/typescript/overview.html#project-setup).

npx shadcn-vue@latest add button


# how to create a page connecting to your API
1. create a page (vue file)
example:
go to pages/admin/hrh/Indicators.vue

2. create a route to your page
go to router/router.ts
example:
import Indicators from '@/pages/admin/hrh/Indicators.vue';
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/authStore'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/indicators',
      name: 'indicators',
      component: Indicators,
    },
  ]
})


let isInitialized = true
export default router

3. check if the page is accessible on the browser
http://localhost:[port]/indicators

4. enter the content of Indicators.vue file
example: 

