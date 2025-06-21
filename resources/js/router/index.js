import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';
import People from '../pages/People.vue';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import PeopleForm from '../components/People/PeopleForm.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Login,
        meta: { requiresAuth: false }
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresAuth: false }
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: { requiresAuth: false }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/people',
        name: 'People',
        component: People,
        meta: { requiresAuth: true }
    },
    {
        path: '/people/new',
        name: 'PeopleNew',
        component: PeopleForm,
        meta: { requiresAuth: true }
    },
    {
        path: '/people/edit/:id',
        name: 'PeopleEdit',
        component: PeopleForm,
        props: route => ({ personId: route.params.id }),
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('auth_token');

    if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
        next({ name: 'Login' });
    } else if (!to.matched.some(record => record.meta.requiresAuth) && isAuthenticated && (to.name === 'Login' || to.name === 'Home')) {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;