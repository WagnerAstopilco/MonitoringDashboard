import { createRouter, createWebHistory } from 'vue-router'
import AdminLayout from '@/layouts/adminLayout.vue'
import DashboardView from '@/views/main/dashboard.vue'
import SalesBoardView from '@/views/main/salesboard.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
        path: '/',
        component: AdminLayout,
        children:[
            {
                path:'dashboard',
                name:'dashboard',
                component:DashboardView
            },
            {
                path:'salesboard',
                name:'salesboard',
                component:SalesBoardView
            }
        ]
    }
  ],
})

export default router
