import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

import Navbar from '~/components/Navbar.vue';
import Home from '~/components/Home.vue';
import Footer from '~/components/Footer.vue';
import Login from '~/components/Login.vue';
import Register from '~/components/Register.vue';
import ResetPassword from '~/components/ResetPassword.vue';

const checkAuth = (to, from, next) => {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user && user.token) {
        next();
    } else {
        next('/login');
    }
}

const isUserLoggedIn = (to, from, next) => {
    const user = JSON.parse(localStorage.getItem('user'));
    if (user && user.token) {
        next('/');
    } else {
        next();
    }
}

const router = new Router({
    routes: [
        { path: '/', components: { header: Navbar, default: Home, footer: Footer }, meta: { title: 'Home' }, beforeEnter: checkAuth },
        { path: '/login', components: { default: Login }, meta: { title: 'Login' }, beforeEnter: isUserLoggedIn },
        { path: '/register', components: { default: Register }, meta: { title: 'Register' }, beforeEnter: isUserLoggedIn },
        { path: '/reset-password', components: { default: ResetPassword }, meta: { title: 'Reset Password' }, beforeEnter: isUserLoggedIn },
        { path: '/*', redirect: '/' }
    ]
});

router.beforeEach((to, from, next) => {
    const pageTitle = to.meta.title;
    document.title = `${pageTitle} | Client Intranet`;
    next();
});

export default function initRouter() {
    return router;
}