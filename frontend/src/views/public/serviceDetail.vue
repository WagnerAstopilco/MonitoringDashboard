<template>
    <main class="service-detail-page">

        <!-- LOADING -->
        <Preloader :visible="cargando" v-if="cargando"></Preloader>

        <!-- CONTENT -->
        <template v-else-if="service">

            <!-- HERO / MAIN SERVICE DETAIL -->
            <section class="service-hero py-5">
                <div class="container">

                    <div class="d-flex justify-content-center p-4">
                        <h1 class="display fw-bold mt-2 mb-4">Detalles del servicio</h1>
                    </div>

                    <div class="row g-5 align-items-center">

                        <!-- IMAGE -->
                        <div class="col-lg-6">

                            <div class="service-image-wrapper">

                                <img
                                    v-if="serviceImage"
                                    :src="serviceImage"
                                    :alt="service.name"
                                    class="service-image"
                                >

                                <div
                                    v-else
                                    class="service-image-placeholder"
                                >
                                    <i class="bi bi-briefcase"></i>

                                    <span>
                                        {{ service.name }}
                                    </span>
                                </div>

                            </div>

                        </div>


                        <!-- INFORMATION -->
                        <div class="col-lg-6">

                            <!-- STATUS -->
                            <div
                                v-if="serviceState"
                                class="service-status mb-3"
                                :class="serviceState.class"
                            >
                                <i :class="serviceState.icon"></i>

                                <span>
                                    {{ serviceState.label }}
                                </span>
                            </div>


                            <!-- NAME -->
                            <h1 class="display-5 fw-bold mb-4">
                                {{ service.name }}
                            </h1>


                            <!-- PRICE -->
                            <div class="price-box mb-4">

                                <div class="price-value">
                                    S/. {{ formatPrice(service.price) }}
                                </div>

                            </div>

                            <!-- CTA -->
                            <div class="service-actions mt-4">

                                <button
                                    v-if="isAvailable"
                                    type="button"
                                    class="btn btn-dark btn-lg px-4"
                                    @click="contactWhatsApp"
                                >
                                    <i class="bi bi-whatsapp me-2"></i>
                                    Solicitar servicio
                                </button>

                                <button
                                    v-else
                                    type="button"
                                    class="btn btn-outline-dark btn-lg px-4"
                                    @click="contactWhatsApp"
                                >
                                    <i class="bi bi-chat-dots me-2"></i>
                                    Consultar servicio
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-link text-dark"
                                    @click="goBack"
                                >
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Ver servicios
                                </button>

                            </div>

                        </div>

                    </div>

                </div>
            </section>


            <!-- DESCRIPTION -->
            <section class="description-section py-5">

                <div class="container py-4">

                    <div class="row justify-content-center">

                        <div class="col-lg-9">

                            <div class="description-content">

                                <span class="section-label">
                                    SOBRE ESTE SERVICIO
                                </span>

                                <h2 class="display-6 fw-bold mt-2 mb-4">
                                    Todo lo que necesitas saber
                                </h2>

                                <div
                                    v-if="service.description"
                                    class="description-text"
                                >
                                    {{ service.description }}
                                </div>

                                <div
                                    v-else
                                    class="description-text text-muted"
                                >
                                    Conoce todos los detalles de este servicio
                                    y descubre cómo podemos ayudarte a impulsar
                                    la imagen de tu negocio.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

            <!-- PROMOTIONS -->
            <section v-if="service.promotions?.length" class="promotion-section py-5 bg-light">
                <div class="container py-4">

                    <div class="section-heading text-center mb-5">

                        <h2 class="display-6 fw-bold mt-2">
                            Promociones vinculadas
                        </h2>

                        <p class="text-muted mx-auto mt-3">
                            Conoce las promociones que tiene este servicio.
                        </p>

                    </div>


                    <div class="row g-4">

                        <div v-for="promotion in service.promotions" :key="service.id" class="col-md-6 col-lg-4">

                            <div class="included-promotion h-100" @click="goToPromotionDetail(promotion.id)">

                                <div class="included-promotion-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>

                                <div>

                                    <h3 class="h5 fw-bold mb-2">
                                        {{ promotion.name }}
                                    </h3>

                                    <p v-if="promotion.description" class="text-muted mb-0">
                                        {{ promotion.description }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>

            <!-- CTA -->
            <section class="cta-section py-5">

                <div class="container py-4">

                    <div class="cta-content rounded-4 p-5 text-center">

                        <span class="text-uppercase small fw-semibold">
                            ¿Tienes alguna consulta?
                        </span>

                        <h2 class="display-6 fw-bold mt-2 mb-3">
                            ¿Te interesa algun otro servicio?
                        </h2>

                        <p class="mb-4">
                            Escríbenos y te brindaremos toda la información
                            que necesitas sobre nuestros servicios.
                        </p>

                        <button
                            type="button"
                            class="btn btn-light btn-lg px-5"
                            @click="contactWhatsApp"
                        >
                            <i class="bi bi-whatsapp me-2"></i>
                            Contáctanos
                        </button>

                    </div>

                </div>

            </section>

        </template>


        <!-- ERROR -->
        <section v-else class="error-section">

            <div class="container">

                <div class="error-card text-center">

                    <div class="error-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>

                    <h2 class="fw-bold mt-4">
                        No pudimos cargar el servicio
                    </h2>

                    <p class="text-muted">
                        {{ error }}
                    </p>

                    <button
                        type="button"
                        class="btn btn-dark px-4"
                        @click="goBack"
                    >
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a servicios
                    </button>

                </div>

            </div>

        </section>

    </main>
</template>
<script setup>
import Preloader from '@/components/layout/preloader.vue'
import ServicesService from '@/services/ServicesService'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const cargando = ref(false)
const service = ref(null)
const error = ref(null)


// ========================================
// OBTENER SERVICIO
// ========================================

const getService = async () => {

    try {

        cargando.value = true
        error.value = null

        const response = await ServicesService.getPublicServiceDetails(
            route.params.idservice
        )

        service.value = response.data.data

    } catch (err) {

        console.error('Error al obtener el servicio:', err)

        error.value = 'No se pudo cargar la información del servicio.'

    } finally {

        cargando.value = false

    }

}


// ========================================
// FORMATEAR PRECIO
// ========================================

const formatPrice = (price) => {

    if (price === null || price === undefined) {
        return '0.00'
    }

    return Number(price).toFixed(2)

}


// ========================================
// ESTADO DEL SERVICIO
// ========================================

const serviceState = computed(() => {

    if (!service.value) {
        return null
    }

    if (service.value.status !== 'active') {

        return {
            label: 'Servicio inactivo',
            class: 'inactive',
            icon: 'bi bi-x-circle'
        }

    }

    return {
        label: 'Servicio disponible',
        class: 'active',
        icon: 'bi bi-check-circle'
    }

})


// ========================================
// DISPONIBILIDAD
// ========================================

const isAvailable = computed(() => {

    return serviceState.value?.class === 'active'

})


// ========================================
// IMAGEN DEL SERVICIO
// ========================================

const serviceImage = computed(() => {

    if (!service.value?.service_image) {
        return null
    }

    return service.value.service_image

})


// ========================================
// WHATSAPP
// ========================================

const contactWhatsApp = () => {

    if (!service.value) {
        return
    }

    const phone = '51952635357'

    const message = `Hola, estoy interesado en el servicio "${service.value.name}". Me gustaría obtener más información.`

    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`

    window.open(url, '_blank')

}


// ========================================
// VOLVER A SERVICIOS
// ========================================

const goBack = () => {

    router.push({
        name: 'publicServices'
    })

}


// ========================================
// CONTACTO GENERAL
// ========================================

const contactUs = () => {

    router.push({
        name: 'contact'
    })

}

const goToPromotionDetail=(promotionId)=>{
    router.push({name:'publicPromotionDetails',params:{idpromotion:promotionId}})
}
// ========================================
// MOUNTED
// ========================================

onMounted(() => {

    getService()

})
</script>

<style scoped>
.service-detail-page {
    overflow: hidden;
}


/* ========================================
   HERO
======================================== */

.service-hero {
    background: #fff;
}

.service-image-wrapper {
    width: 100%;
    min-height: 500px;
    border-radius: 24px;
    overflow: hidden;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.service-image {
    width: 100%;
    height: 500px;
    object-fit: contain;
    display: block;
}

.service-image-placeholder {
    width: 100%;
    height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
}

.service-image-placeholder i {
    font-size: 5rem;
    margin-bottom: 15px;
}

.service-image-placeholder span {
    font-size: 1rem;
    font-weight: 500;
}


/* ========================================
   STATUS
======================================== */

.service-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.service-status.active {
    background: #e8f7ee;
    color: #198754;
}

.service-status.inactive {
    background: #f8d7da;
    color: #842029;
}


/* ========================================
   PRICE
======================================== */

.price-box {
    display: inline-flex;
    align-items: baseline;
    gap: 12px;
    padding: 14px 22px;
    border-radius: 14px;
    background: var(--bs-primary);
    color: white;
}

.price-label {
    font-size: 0.9rem;
    opacity: 0.9;
    font-weight: 500;
}

.price-value {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}


/* ========================================
   SUMMARY
======================================== */

.service-summary {
    font-size: 1.05rem;
    line-height: 1.8;
    max-width: 650px;
}


/* ========================================
   ACTIONS
======================================== */

.service-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}


/* ========================================
   SECTIONS
======================================== */

.section-heading {
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.section-label {
    color: var(--bs-primary);
    font-weight: 600;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}


/* ========================================
   DESCRIPTION
======================================== */

.description-section {
    background: #fff;
}

.description-content {
    padding: 20px 0;
}

.description-text {
    color: #6c757d;
    font-size: 1.05rem;
    line-height: 1.9;
    white-space: pre-line;
}


/* ========================================
   SERVICE INFORMATION
======================================== */

.service-info-section {
    background: #f8f9fa;
}

.service-info-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    padding: 25px;
    display: flex;
    align-items: flex-start;
    gap: 18px;
    transition: all 0.25s ease;
}

.service-info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
}

.service-info-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    background: var(--bs-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.service-info-price {
    font-size: 1.15rem;
}

/* ========================================
   SERVICES
======================================== */

.included-promotion {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    padding: 25px;
    display: flex;
    gap: 18px;
    transition: all 0.25s ease;
}

.included-promotion:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
}

.included-promotion-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    background: var(--bs-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}


/* ========================================
   CTA
======================================== */

.cta-section {
    background: #fff;
}

.cta-content {
    background: #212529;
    color: white;
}

.cta-content p {
    color: rgba(255, 255, 255, 0.75);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}


/* ========================================
   ERROR
======================================== */

.error-section {
    padding: 100px 0;
}

.error-card {
    max-width: 600px;
    margin: auto;
}

.error-icon {
    width: 70px;
    height: 70px;
    margin: auto;
    border-radius: 50%;
    background: #f8d7da;
    color: #842029;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}


/* ========================================
   RESPONSIVE
======================================== */

@media (max-width: 991.98px) {

    .service-image-wrapper,
    .service-image,
    .service-image-placeholder {
        min-height: 400px;
        height: 400px;
    }

    .service-hero {
        padding-top: 30px !important;
    }
}


@media (max-width: 575.98px) {

    .service-image-wrapper,
    .service-image,
    .service-image-placeholder {
        min-height: 300px;
        height: 300px;
    }

    .service-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .service-actions .btn {
        width: 100%;
    }

    .price-box {
        padding: 12px 18px;
    }

    .price-value {
        font-size: 1.7rem;
    }

    .service-info-card {
        padding: 20px;
    }

    .cta-content {
        padding: 35px 25px !important;
    }
}
</style>