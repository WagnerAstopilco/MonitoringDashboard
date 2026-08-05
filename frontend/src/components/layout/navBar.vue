    <script setup>

const menu=[
    {
        name:'Dashboard',
        route:'/dashboard'
    },
    {
        name:'Pizarra',
        route:'/salesboard'
    },
    {
        name:'Clientes',
        route:'/clients'
    },
    {
        name:'Servicios',
        route:'/services'
    },
    {
        name:'Promociones',
        route:'/promotions'
    },
    {
        name:'Transacciones',
        route:'/transactions'
    },
    {
        name:'Usuarios',
        route:'/users'
    },
]
const logout = () => {

    console.log('Cerrar sesión')

}

</script>


<template>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container-fluid">


    <!-- Brand -->

    <RouterLink
        class="navbar-brand"
        to="/dashboard"
    >
    <div>
        <img src="@/assets/images/logo.png" alt="Logo Gato Negro" class="footer-logo mb-3" style="max-width:36px; height: auto; border-radius: 50%;" />
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

                Usuario

            </button>


            <ul class="dropdown-menu dropdown-menu-end">


                <li>

                    <RouterLink
                        class="dropdown-item"
                        to="/profile"
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