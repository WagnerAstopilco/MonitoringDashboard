import { createRouter, createWebHistory } from "vue-router";
import { getToken, getUser } from '@/utils/authStorage.js';
import AdminLayout from "@/layouts/adminLayout.vue";
import DashboardView from "@/views/main/dashboard.vue";
import SalesBoardView from "@/views/main/salesboard.vue";
import ClientsView from "@/views/clients/clients.vue";
import ServicesView from "@/views/services/services.vue";
import PromotionsView from "@/views/promotions/promotions.vue";
import TransactionsView from "@/views/transactions/transactions.vue";
import UsersView from "@/views/users/users.vue";
import ProfileView from "@/views/users/profile.vue";
import PublicLayout from "@/layouts/publicLayout.vue";
import LoginView from "@/views/auth/login.vue";
import PublicServicesView from "@/views/public/services.vue";
import PublicPromotionsView from "@/views/public/promotions.vue";
import ForgotPasswordView from "@/views/auth/forgotPass.vue";

export const getHomeRouteByRole = (role) => {

    switch (role) {

        case 'admin':
            return { name: 'dashboard' };

        case 'employee':
            return { name: 'salesboard' };

        case 'visit':
            return { name: 'dashboard' };

        default:
            // rol desconocido -> lo mandamos a la vista publica, no a una ruta inexistente
            return { name: 'publicServices' };

    }

};
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/home",
            component: AdminLayout,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    path: "",
                    redirect: "/home/dashboard",
                },
                {
                    path: "dashboard",
                    name: "dashboard",
                    component: DashboardView,
                },
                {
                    path: "salesboard",
                    name: "salesboard",
                    component: SalesBoardView,
                },
                {
                    path: "clients",
                    name: "clients",
                    component: ClientsView,
                },
                {
                    path: "services",
                    name: "services",
                    component: ServicesView,
                },
                {
                    path: "promotions",
                    name: "promotions",
                    component: PromotionsView,
                },
                {
                    path: "transactions",
                    name: "transactions",
                    component: TransactionsView,
                },
                {
                    path: "users",
                    name: "users",
                    component: UsersView,
                },
            ],
        },
        {
            path: "/user/profile",
            name: "profile",
            component: ProfileView,
            meta: {
                requiresAuth: true
            },
        },
        {
            path: "/",
            component: PublicLayout,
            children: [
                {
                    path: "",
                    redirect: "/services",
                },
                {
                    path: "services",
                    name: "publicServices",
                    component: PublicServicesView,
                    meta: {
                        guestOnly: true
                    },
                },
                {
                    path: "promotions",
                    name: "publicPromotions",
                    component: PublicPromotionsView,
                    meta: {
                        guestOnly: true
                    },
                },
                {
                    path: "login",
                    name: "login",
                    component: LoginView,
                    meta: {
                        guestOnly: true
                    },
                },
            ],
        },
        {
            path: "/forgot-password",
            name: "forgotPassword",
            component: ForgotPasswordView,
        },
    ],
});

router.beforeEach((to) => {

    const token = getToken();

    const user = getUser();


    // Usuario no autenticado intentando acceder a una ruta protegida

    if (to.meta.requiresAuth && !token) {

        return {
            name: 'login'
        };

    }


    // Usuario autenticado intentando acceder al login

    if (to.meta.guestOnly && token && user) {

        return getHomeRouteByRole(user.role);

    }


    return true;

});
export default router;
