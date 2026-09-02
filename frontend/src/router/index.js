import { createRouter, createWebHistory } from 'vue-router'

import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import Star from '../views/Star.vue'
import Terbilang from '../views/Terbilang.vue'
import InputData from '../views/InputData.vue'

const routes = [
    {
        path: '/',
        redirect: '/login',
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/star',
        name: 'Star',
        component: Star,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/terbilang',
        name: 'Terbilang',
        component: Terbilang,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: '/input-data',
        name: 'InputData',
        component: InputData,
        meta: {
            requiresAuth: true,
        },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth && !token) {
        next('/login')
        return
    }

    if (to.path === '/login' && token) {
        next('/dashboard')
        return
    }

    next()
})

export default router