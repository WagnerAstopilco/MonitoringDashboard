<template>
    <main class="about-page">

        <!-- HERO -->
        <section class="about-hero py-5">
            <div class="container py-5">
                <div class="row align-items-center g-5">

                    <div class="col-lg-6">
                        <span class="badge bg-dark mb-3 px-3 py-2">
                            GATO NEGRO
                        </span>

                        <h1 class="display-4 fw-bold mb-4">
                            Hacemos que tu negocio
                            <span class="text-primary"> se vea diferente.</span>
                        </h1>

                        <p class="lead text-muted mb-4">
                            Somos un negocio dedicado al diseño, publicidad e impresión,
                            creando soluciones visuales para empresas, emprendimientos
                            y personas que quieren destacar.
                        </p>

                        <router-link :to="{ name: 'publicServices' }" class="btn btn-dark btn-lg px-4">
                            Conoce nuestros servicios
                        </router-link>
                    </div>

                    <div class="col-lg-6">
                        <div class="about-image">
                            <div class="image-placeholder">
                                <img src="@/assets/images/nosotros.jpg" class="w-100 h-100 object-fit-cover rounded-4"
                                    alt="Logo Gato Negro" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- QUIENES SOMOS -->
        <section class="py-5 bg-light">
            <div class="container py-5">

                <div class="row justify-content-center text-center mb-5">
                    <div class="col-lg-8">

                        <span class="text-primary fw-semibold">
                            SOBRE NOSOTROS
                        </span>

                        <h2 class="display-6 fw-bold mt-2">
                            Más que publicidad, creamos identidad
                        </h2>

                        <p class="text-muted mt-3">
                            En Gato Negro creemos que la imagen de un negocio es parte
                            fundamental de su crecimiento. Por eso combinamos creatividad,
                            diseño y producción para crear soluciones que ayuden a nuestros
                            clientes a presentar mejor sus productos y servicios.
                        </p>

                    </div>
                </div>

                <div class="row align-items-center g-5">

                    <div class="col-lg-6">
                        <div class="about-card p-4 p-lg-5 bg-white rounded-4 shadow-sm">

                            <h3 class="fw-bold mb-3">
                                ¿Por qué Gato Negro?
                            </h3>

                            <p class="text-muted">
                                Cada negocio tiene algo que lo hace especial. Nuestro trabajo
                                consiste en convertir esa idea en una imagen que pueda ser
                                reconocida y recordada.
                            </p>

                            <p class="text-muted mb-0">
                                Desde una tarjeta de presentación hasta un banner publicitario,
                                buscamos que cada pieza tenga un propósito y represente
                                correctamente a tu negocio.
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-6">

                        <div v-for="(value, index) in values" :key="index" class="d-flex mb-4">

                            <div class="value-number me-3">
                                {{ String(index + 1).padStart(2, '0') }}
                            </div>

                            <div>
                                <h4 class="fw-bold mb-1">
                                    {{ value.title }}
                                </h4>

                                <p class="text-muted mb-0">
                                    {{ value.description }}
                                </p>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </section>


        <!-- SERVICIOS -->
        <section id="servicios" class="py-5">
            <div class="container py-5">

                <div class="text-center mb-5">

                    <span class="text-primary fw-semibold">
                        LO QUE HACEMOS
                    </span>

                    <h2 class="display-6 fw-bold mt-2">
                        Soluciones para hacer crecer tu marca
                    </h2>

                    <p class="text-muted mx-auto mt-3" style="max-width: 650px;">
                        Trabajamos diferentes áreas de diseño, publicidad e impresión
                        para ofrecer una solución completa a nuestros clientes.
                    </p>

                </div>


                <div class="row g-4">

                    <div v-for="service in services" :key="service.title" class="col-md-4">

                        <div class="service-card h-100 p-4 rounded-4">

                            <div class="service-icon mb-4">
                                <i :class="service.icon"></i>
                            </div>

                            <h3 class="h4 fw-bold">
                                {{ service.title }}
                            </h3>

                            <p class="text-muted mb-0">
                                {{ service.description }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>
        </section>


        <!-- CTA -->
        <section class="py-5">
            <div class="container py-4">

                <div class="cta-section rounded-4 p-5 text-center">

                    <span class="text-uppercase small fw-semibold">
                        ¿Tienes un proyecto?
                    </span>

                    <h2 class="display-6 fw-bold mt-2 mb-3">
                        Hagamos que tu idea cobre vida
                    </h2>

                    <p class="mb-4">
                        Cuéntanos qué necesitas y encontremos juntos la mejor
                        solución para tu negocio.
                    </p>

                    <button type="button" class="btn btn-light btn-lg px-5" @click="contactUs">
                        Contáctanos
                    </button>

                </div>

            </div>
        </section>

    </main>
</template>

<script setup>
import { ref } from 'vue'
import router from '@/router'

const services = ref([
    {
        icon: 'bi bi-palette',
        title: 'Diseño gráfico',
        description:
            'Creamos piezas visuales que representan la identidad de tu negocio y ayudan a comunicar tus ideas de manera efectiva.'
    },
    {
        icon: 'bi bi-megaphone',
        title: 'Publicidad',
        description:
            'Desarrollamos material publicitario pensado para que tu negocio destaque y llegue a más personas.'
    },
    {
        icon: 'bi bi-printer',
        title: 'Impresión',
        description:
            'Convertimos tus diseños en productos físicos de calidad, desde tarjetas y flyers hasta banners y viniles.'
    }
])

const values = ref([
    {
        title: 'Creatividad',
        description: 'Buscamos ideas diferentes que hagan que cada proyecto tenga una identidad propia.'
    },
    {
        title: 'Calidad',
        description: 'Cuidamos cada detalle, desde el diseño hasta el resultado final de cada producto.'
    },
    {
        title: 'Compromiso',
        description: 'Trabajamos para cumplir con los objetivos y necesidades de cada cliente.'
    }
])

const contactUs = () => {
    router.push({ name: 'contact' })
}
</script>

<style scoped>
.about-page {
    overflow: hidden;
}

/* HERO */

.about-hero {
    min-height: 650px;
    display: flex;
    align-items: center;
}

.about-image {
    min-height: 400px;
}

.image-placeholder {
    width: 100%;
    height: 400px;
    border-radius: 24px;
    background: #f1f1f1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 1.1rem;
}

.image-placeholder i {
    font-size: 4rem;
    margin-bottom: 15px;
}


/* VALUES */

.value-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bs-primary);
    min-width: 45px;
}


/* SERVICES */

.service-card {
    border: 1px solid #e9e9e9;
    transition: all 0.25s ease;
    background: white;
}

.service-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.service-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    background: var(--bs-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}


/* CTA */

.cta-section {
    background: #212529;
    color: white;
}

.cta-section p {
    color: rgba(255, 255, 255, 0.75);
}
</style>