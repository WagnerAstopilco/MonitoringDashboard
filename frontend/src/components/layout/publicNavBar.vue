    <script setup>

const menu=[
    {
        name:'Servicios',
        route:'/services'
    },
    {
        name:'Promociones',
        route:'/promotions'
    },
    {
        name:'Iniciar sesión',
        route:'/login'
    },
]

</script>


<template>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container-fluid">


    <!-- Brand -->

    <RouterLink
        class="navbar-brand"
        to="/services"
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
    </div>


</div>

</nav>

</template>