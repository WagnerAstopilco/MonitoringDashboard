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
import NewServiceView from "@/views/services/newService.vue";
import ServiceDetailsView from "@/views/services/serviceDetails.vue";
import NewPromotionView from "@/views/promotions/newPromotion.vue";
import PromotionDetailsView from "@/views/promotions/promotionDetails.vue";
import NewTransactionView from "@/views/transactions/newTransaction.vue";
import TransactionDetailsView from "@/views/transactions/transactionsDetails.vue";
import AboutView from "@/views/public/about.vue";
import PublicServiceDetailsView from "@/views/public/serviceDetail.vue";
import PublicPromotionDetailsView from "@/views/public/promotionDetail.vue";
import ContactView from '@/views/public/contact.vue';
import { useAuthStore } from '@/stores/auth'


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
        //demo route
        {
            path:"/demo",
            component:AdminLayout,
            meta:
            {
                requiresAuth:true
            }
        },
        //administrative routes
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
                    meta: {
                        permission: 'dashboard.view'
                    }

                },
                {
                    path: "salesboard",
                    name: "salesboard",
                    component: SalesBoardView,
                    meta: {
                        permission: 'salesboard.view'
                    }
                },
                {
                    path: "clients",
                    name: "clients",
                    component: ClientsView,
                    meta: {
                        permission: 'clients.view'
                    }
                },
                {
                    path: "services",
                    name: "services",
                    component: ServicesView,
                    meta: {
                        permission: 'services.view'
                    }
                },
                {
                    path: "promotions",
                    name: "promotions",
                    component: PromotionsView,
                    meta: {
                        permission: 'promotions.view'
                    }
                },
                {
                    path: "transactions",
                    name: "transactions",
                    component: TransactionsView,
                    meta: {
                        permission: 'transactions.view'
                    }
                },
                {
                    path: "users",
                    name: "users",
                    component: UsersView,
                    meta: {
                        permission: 'users.view'
                    }
                },
            ],
        },
        //public routes
        {
            path: "/",
            component: PublicLayout,
            children: [
                {
                    path: "",
                    redirect: "/services",
                },
                {
                    path: "about",
                    name: "about",
                    component: AboutView,
                    meta: {
                        guestOnly: true
                    },
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
                    path: "servicedetails/:idservice?",
                    name: "publicServiceDetails",
                    component: PublicServiceDetailsView,
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
                    path: "promodetails/:idpromotion?",
                    name: "publicPromotionDetails",
                    component: PublicPromotionDetailsView,
                    meta: {
                        guestOnly: true
                    },
                },
                {
                    path: "contact",
                    name: "contact",
                    component: ContactView,
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
        //profile route
        {
            path: "/home/profile",
            component: AdminLayout,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    path: "",
                    name: "profile",
                    component: ProfileView,
                },
            ]
        },
        //services routes
        {
            path: "/home/services",
            component: AdminLayout,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    path: "newService",
                    name: "newService",
                    component: NewServiceView,
                    meta: {
                        permission: 'services.create'
                    },
                },
                {
                    path: "details/:idservice?",
                    name: "serviceDetails",
                    component: ServiceDetailsView,
                    meta: {
                        permission: 'services.view'
                    },
                },

            ]
        },
        //promotions routes
        {
            path: "/home/promotions",
            component: AdminLayout,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    path: "newPromotion",
                    name: "newPromotion",
                    component: NewPromotionView,
                    meta:{
                        permission:'promotions.create'
                    }
                },
                {
                    path: "details/:idpromotion?",
                    name: "promotionDetails",
                    component: PromotionDetailsView,
                    meta:{
                        permission:'promotions.view'
                    }
                },
            ]
        },
        //transactions routes
        {
            path: "/home/transactions",
            component: AdminLayout,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    path: "newTransaction",
                    name: "newTransaction",
                    component: NewTransactionView,
                    meta:{
                        permission:'transactions.create'
                    }
                },
                {
                    path: "details/:idtransaction?",
                    name: "transactionDetails",
                    component: TransactionDetailsView,
                    meta:{
                        permission:'transactions.view'
                    }
                },
            ]
        }
    ],
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        }

        return { top: 0 }
    }

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
    if (to.meta.permission) {

        const auth = useAuthStore()

        if (!auth.hasPermission(to.meta.permission)) {

            return getHomeRouteByRole(user.role)

        }

    }

    return true;

});
export default router;
