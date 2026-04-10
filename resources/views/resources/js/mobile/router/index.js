import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Login from '../views/Login.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { hideNav: true, guest: true }
    },
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/operations',
        name: 'Operations',
        component: () => import('../views/OperationsList.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/operations/create',
        name: 'CreateOperation',
        component: () => import('../views/CreateOperation.vue'),
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory('/mobile-app'), // Base URL pour la PWA
    routes
});

// Garde de navigation pour l'auth
router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('auth_token');

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

export default router;
