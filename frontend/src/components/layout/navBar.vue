<template>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container-fluid">


    <!-- Brand -->

    <RouterLink
        class="navbar-brand"
        to="/home/dashboard"
    >
    <div>
        Gato Negro
    </div>
        
    </RouterLink>



    <!-- Mobile button -->

    <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarMenu"
    >

        <span class="navbar-toggler-icon"></span>

    </button>



    <div
        class="collapse navbar-collapse"
        id="navbarMenu"
    >


        <!-- Menu principal -->

        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">


            <li
                class="nav-item"
                v-for="item in menu"
                :key="item.route"
            >

                <RouterLink
                    class="nav-link"
                    :to="item.route"
                >

                    {{item.name}}

                </RouterLink>


            </li>


        </ul>



        <!-- Usuario -->

        <div class="dropdown ms-lg-3">


            <button
                class="btn btn-dark dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
            >

                <i class="bi bi-person-circle"></i>

                {{ auth.user?.username }}

            </button>


            <ul class="dropdown-menu dropdown-menu-end">


                <li>

                    <RouterLink
                        class="dropdown-item"
                        to="/user/profile"
                    >
                        Perfil
                    </RouterLink>

                </li>


                <li>
                    <hr class="dropdown-divider">
                </li>


                <li>

                    <button
                        class="dropdown-item text-danger"
                        @click="logout"
                    >

                        Cerrar sesión

                    </button>

                </li>


            </ul>


        </div>


    </div>


</div>

</nav>

</template>

<script setup>

import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'


const router = useRouter()

const auth = useAuthStore()


const menu=[
    {
        name:'Dashboard',
        route:'/home/dashboard'
    },
    {
        name:'Pizarra',
        route:'/home/salesboard'
    },
    {
        name:'Clientes',
        route:'/home/clients'
    },
    {
        name:'Servicios',
        route:'/home/services'
    },
    {
        name:'Promociones',
        route:'/home/promotions'
    },
    {
        name:'Transacciones',
        route:'/home/transactions'
    },
    {
        name:'Usuarios',
        route:'/home/users'
    },
]


const logout = async()=>{

    // auth.logout() ya llama a AuthServices.logout(), limpia token/user
    // del store y de localStorage (incluso si el request falla, por el finally)
    await auth.logout()

    router.push('/')

}


</script>