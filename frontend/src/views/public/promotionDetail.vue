<template>
    <main class="promotion-detail-page">

        <!-- LOADING -->
        <Preloader :visible="cargando" v-if="cargando"></Preloader>

        <!-- CONTENT -->
        <template v-else-if="promotion">

            <!-- HERO / MAIN PRODUCT DETAIL -->
            <section class="promotion-hero py-5">
                <div class="container">
                    <div class="d-flex justify-content-center p-4">
                        <h1 class="display fw-bold mt-2 mb-4">Detalles de la promoción</h1>
                    </div>
                    <div class="row g-5 align-items-center">

                        <!-- IMAGE -->
                        <div class="col-lg-6">

                            <div class="promotion-image-wrapper">

                                <img v-if="promotionImage" :src="promotionImage" :alt="promotion.name"
                                    class="promotion-image">

                                <div v-else class="promotion-image-placeholder">
                                    <i class="bi bi-megaphone"></i>

                                    <span>
                                        Promoción Gato Negro
                                    </span>
                                </div>

                            </div>

                        </div>


                        <!-- INFORMATION -->
                        <div class="col-lg-6">

                            <!-- STATUS -->
                            <div v-if="promotionState" class="promotion-status mb-3" :class="promotionState.class">
                                <i :class="promotionState.icon"></i>

                                <span>
                                    {{ promotionState.label }}
                                </span>
                            </div>


                            <!-- NAME -->
                            <h1 class="display-5 fw-bold mb-4">
                                {{ promotion.name }}
                            </h1>


                            <!-- DISCOUNT -->
                            <div class="discount-box mb-4">

                                <div class="discount-value">
                                    {{ discountLabel }}
                                </div>

                                <div class="discount-description">
                                    {{ discountDescription }}
                                </div>

                            </div>


                            <!-- DESCRIPTION -->
                            <!-- <p v-if="promotion.description" class="promotion-summary text-muted">
                                {{ promotion.description }}
                            </p> -->


                            <!-- DATES -->
                            <div class="promotion-dates mt-4">

                                <div class="date-item">

                                    <div class="date-icon">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>

                                    <div>
                                        <span class="date-label">
                                            Inicio
                                        </span>

                                        <strong>
                                            {{ formatDate(promotion.start_date) }}
                                        </strong>
                                    </div>

                                </div>


                                <div class="date-separator">
                                    <i class="bi bi-arrow-right"></i>
                                </div>


                                <div class="date-item">

                                    <div class="date-icon">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>

                                    <div>
                                        <span class="date-label">
                                            Finaliza
                                        </span>

                                        <strong>
                                            {{ formatDate(promotion.end_date) }}
                                        </strong>
                                    </div>

                                </div>

                            </div>


                            <!-- CTA -->
                            <div class="promotion-actions mt-4">

                                <button v-if="isAvailable" type="button" class="btn btn-dark btn-lg px-4"
                                    @click="contactWhatsApp">
                                    <i class="bi bi-whatsapp me-2"></i>
                                    Solicitar promoción
                                </button>

                                <button v-else type="button" class="btn btn-outline-dark btn-lg px-4"
                                    @click="contactWhatsApp">
                                    <i class="bi bi-chat-dots me-2"></i>
                                    Consultar promoción
                                </button>

                                <button type="button" class="btn btn-link text-dark" @click="goBack">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Ver promociones
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
                                    SOBRE ESTA PROMOCIÓN
                                </span>

                                <h2 class="display-6 fw-bold mt-2 mb-4">
                                    Todo lo que necesitas para destacar
                                </h2>

                                <div v-if="promotion.description" class="description-text">
                                    {{ promotion.description }}
                                </div>

                                <div v-else class="description-text text-muted">
                                    Conoce todos los servicios incluidos
                                    en esta promoción y aprovecha esta
                                    oportunidad para impulsar la imagen
                                    de tu negocio.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>

            <!-- SERVICES -->
            <section v-if="promotion.services?.length" class="services-section py-5 bg-light">
                <div class="container py-4">

                    <div class="section-heading text-center mb-5">

                        <h2 class="display-6 fw-bold mt-2">
                            Servicios vinculados
                        </h2>

                        <p class="text-muted mx-auto mt-3">
                            Esta promoción aplica para los siguientes servicios
                            de Gato Negro.
                        </p>

                    </div>


                    <div class="row g-4">

                        <div v-for="service in promotion.services" :key="service.id" class="col-md-6 col-lg-4">

                            <div class="included-service h-100" @click="goToServiceDetail(service.id)">

                                <div class="included-service-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>

                                <div>

                                    <h3 class="h5 fw-bold mb-2">
                                        {{ service.name }}
                                    </h3>

                                    <p v-if="service.description" class="text-muted mb-0">
                                        {{ service.description }}
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

                        <button type="button" class="btn btn-light btn-lg px-5" @click="contactUs">
                            <i class="bi bi-whatsapp me-2"></i>
                            Contáctanos
                        </button>

                    </div>

                </div>
            </section>

        </template>

        <!-- ERROR -->
        <section v-else="error" class="error-section">
            <div class="container">
                <div class="error-card text-center">

                    <div class="error-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>

                    <h2 class="fw-bold mt-4">
                        No pudimos cargar la promoción
                    </h2>

                    <p class="text-muted">
                        {{ error }}
                    </p>

                    <button type="button" class="btn btn-dark px-4" @click="goBack">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver a promociones
                    </button>

                </div>
            </div>
        </section>



    </main>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import PromotionService from '@/services/PromotionService'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const cargando = ref(false);
const promotion = ref(null)
const error = ref(null)

const getPromotion = async () => {
    try {
        cargando.value = true
        error.value = null

        const response = await PromotionService.getPublicPromotionDetails(route.params.idpromotion);

        promotion.value = response.data.data;
    } catch (err) {
        console.error('Error al obtener la promoción:', err)

        error.value = 'No se pudo cargar la información de la promoción.'
    } finally {
        cargando.value = false
    }
}

const discountLabel = computed(() => {
    if (!promotion.value) return ''

    if (promotion.value.discount_type === 'percentage') {
        return `${Number(promotion.value.discount_value)}%`
    }

    return `S/. ${Number(promotion.value.discount_value).toFixed(2)}`
})

const discountDescription = computed(() => {
    if (!promotion.value) return ''

    return 'OFF'
})

const formatDate = (date) => {
    if (!date) return ''

    const [year, month, day] = date.split('-')

    const dateObject = new Date(year, month - 1, day)

    return dateObject.toLocaleDateString('es-PE', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

const contactWhatsApp = () => {
    const phone = '51952635357'

    const message = `Hola, estoy interesado en la promoción "${promotion.value.name}". Me gustaría obtener más información.`

    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`

    window.open(url, '_blank')
}

const goToServiceDetail = (idservice) => {
    router.push({ name: 'publicServiceDetails', params: { idservice: idservice } })
}

const promotionState = computed(() => {
    if (!promotion.value) return null

    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const start = new Date(`${promotion.value.start_date}T00:00:00`)
    const end = new Date(`${promotion.value.end_date}T23:59:59`)

    if (promotion.value.status !== 'active') {
        return {
            label: 'Promoción inactiva',
            class: 'inactive',
            icon: 'bi bi-x-circle'
        }
    }

    if (today < start) {
        return {
            label: 'Próximamente',
            class: 'upcoming',
            icon: 'bi bi-clock'
        }
    }

    if (today > end) {
        return {
            label: 'Promoción finalizada',
            class: 'expired',
            icon: 'bi bi-calendar-x'
        }
    }

    return {
        label: 'Promoción activa',
        class: 'active',
        icon: 'bi bi-check-circle'
    }
})

const isAvailable = computed(() => {
    return promotionState.value?.class === 'active'
})

const promotionImage = computed(() => {
    if (!promotion.value?.promotion_image) {
        return null
    }

    return promotion.value.promotion_image
})

const goBack = () => {
    router.push({ name: 'publicPromotions' })
}

const contactUs = () => {
    router.push({ name: 'contact' })
}

onMounted(() => {
    getPromotion()
})
</script>

<style scoped>
.promotion-detail-page {
    overflow: hidden;
}


/* ========================================
   BREADCRUMB
======================================== */

.breadcrumb-section {
    padding: 20px 0;
    border-bottom: 1px solid #eeeeee;
    background: #fff;
}

.breadcrumb {
    font-size: 0.9rem;
}

.breadcrumb a {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb a:hover {
    color: var(--bs-primary);
}


/* ========================================
   HERO
======================================== */

.promotion-hero {
    background: #fff;
}

.promotion-image-wrapper {
    width: 100%;
    min-height: 500px;
    border-radius: 24px;
    overflow: hidden;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.promotion-image {
    width: 100%;
    height: 500px;
    object-fit: contain;
    display: block;
}

.promotion-image-placeholder {
    width: 100%;
    height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
}

.promotion-image-placeholder i {
    font-size: 5rem;
    margin-bottom: 15px;
}


/* ========================================
   STATUS
======================================== */

.promotion-status {
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

.promotion-status.active {
    background: #e8f7ee;
    color: #198754;
}

.promotion-status.inactive {
    background: #f8d7da;
    color: #842029;
}

.promotion-status.upcoming {
    background: #fff3cd;
    color: #856404;
}

.promotion-status.expired {
    background: #e9ecef;
    color: #6c757d;
}


/* ========================================
   DISCOUNT
======================================== */

.discount-box {
    display: inline-flex;
    align-items: baseline;
    gap: 10px;
    padding: 12px 20px;
    border-radius: 14px;
    background: var(--bs-primary);
    color: white;
}

.discount-value {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}

.discount-description {
    font-size: 0.95rem;
    opacity: 0.9;
}


/* ========================================
   SUMMARY
======================================== */

.promotion-summary {
    font-size: 1.05rem;
    line-height: 1.8;
    max-width: 650px;
}


/* ========================================
   DATES
======================================== */

.promotion-dates {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    max-width: 650px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.date-icon {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 12px;
    background: #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bs-primary);
    font-size: 1.1rem;
}

.date-item div:last-child {
    display: flex;
    flex-direction: column;
}

.date-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 2px;
}

.date-item strong {
    font-size: 0.95rem;
}

.date-separator {
    color: #adb5bd;
}


/* ========================================
   ACTIONS
======================================== */

.promotion-actions {
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
   SERVICES
======================================== */

.included-service {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    padding: 25px;
    display: flex;
    gap: 18px;
    transition: all 0.25s ease;
}

.included-service:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
}

.included-service-icon {
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
   DESCRIPTION
======================================== */

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
   LOADING / ERROR
======================================== */

.loading-section {
    min-height: 500px;
    display: flex;
    align-items: center;
}

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

    .promotion-image-wrapper,
    .promotion-image,
    .promotion-image-placeholder {
        min-height: 400px;
        height: 400px;
    }

    .promotion-hero {
        padding-top: 30px !important;
    }
}


@media (max-width: 575.98px) {

    .promotion-image-wrapper,
    .promotion-image,
    .promotion-image-placeholder {
        min-height: 300px;
        height: 300px;
    }

    .promotion-dates {
        flex-direction: column;
        align-items: stretch;
    }

    .date-separator {
        display: none;
    }

    .promotion-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .promotion-actions .btn {
        width: 100%;
    }

    .discount-value {
        font-size: 1.7rem;
    }

    .cta-content {
        padding: 35px 25px !important;
    }
}
</style>