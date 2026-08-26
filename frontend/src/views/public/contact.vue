<script setup>
import { ref } from 'vue'
import {useRouter} from 'vue-router'

/*
|--------------------------------------------------------------------------
| Datos de contacto
|--------------------------------------------------------------------------
*/

const contactEmail = 'wastopilco@gmail.com'
const whatsappNumber = '51952635357'
const phoneNumber = '+51 952 635 357'

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

const form = ref({
    name: '',
    phone: '',
    email: '',
    service: '',
    message: ''
})

const sending = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const router=useRouter();

/*
|--------------------------------------------------------------------------
| Servicios
|--------------------------------------------------------------------------
*/

const services = [
    'Diseño gráfico',
    'Publicidad',
    'Impresión',
    'Banners y viniles',
    'Tarjetas de presentación',
    'Flyers y brochures',
    'Otro'
]

/*
|--------------------------------------------------------------------------
| Enviar formulario
|--------------------------------------------------------------------------
*/

const submitForm = async () => {

    sending.value = true
    successMessage.value = ''
    errorMessage.value = ''

    try {

        const response = await fetch(
            'https://formspree.io/f/mzepodej',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify(form.value)
            }
        )

        if (!response.ok) {
            throw new Error('Error al enviar el formulario')
        }

        successMessage.value =
            '¡Gracias! Tu mensaje fue enviado correctamente. Nos pondremos en contacto contigo pronto.'

        form.value = {
            name: '',
            phone: '',
            email: '',
            service: '',
            message: ''
        }

    } catch (error) {

        console.error(error)

        errorMessage.value =
            'No pudimos enviar tu mensaje. Por favor, inténtalo nuevamente o contáctanos por WhatsApp.'

    } finally {

        sending.value = false

    }
}

/*
|--------------------------------------------------------------------------
| WhatsApp
|--------------------------------------------------------------------------
*/

const openWhatsApp = () => {

    const message = encodeURIComponent(
        'Hola Gato Negro, quisiera información sobre sus servicios.'
    )

    window.open(
        `https://wa.me/${whatsappNumber}?text=${message}`,
        '_blank'
    )
}

const goToContact=()=>{
    router.push({name:'about'})
}
</script>


<template>

    <main class="contact-page">


        <!-- =========================================================
             HERO
        ========================================================== -->

        <section class="contact-hero py-5">

            <div class="container py-5">

                <div class="row justify-content-center text-center">

                    <div class="col-lg-8">

                        <span class="badge bg-dark px-3 py-2 mb-3">
                            GATO NEGRO
                        </span>

                        <h1 class="display-4 fw-bold mb-4">
                            Hablemos de tu
                            <span class="text-primary">
                                próximo proyecto.
                            </span>
                        </h1>

                        <p class="lead text-muted mx-auto">
                            Cuéntanos qué necesitas. Estamos listos para ayudarte
                            con tus proyectos de diseño, publicidad e impresión.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             OPCIONES DE CONTACTO
        ========================================================== -->

        <section class="pb-5">

            <div class="container pb-5">

                <div class="row g-4">


                    <!-- WHATSAPP -->

                    <div class="col-md-6 col-lg-3">

                        <button
                            type="button"
                            class="contact-option w-100 h-100"
                            @click="openWhatsApp"
                        >

                            <div class="contact-option-icon whatsapp">
                                <i class="bi bi-whatsapp"></i>
                            </div>

                            <h3 class="h5 fw-bold mt-4">
                                WhatsApp
                            </h3>

                            <p class="text-muted mb-0">
                                ¿Necesitas una respuesta rápida?
                                Escríbenos directamente.
                            </p>

                        </button>

                    </div>


                    <!-- CORREO -->

                    <div class="col-md-6 col-lg-3">

                        <div class="contact-option h-100">

                            <div class="contact-option-icon">
                                <i class="bi bi-envelope"></i>
                            </div>

                            <h3 class="h5 fw-bold mt-4">
                                Correo electrónico
                            </h3>

                            <p class="text-muted mb-2">
                                Escríbenos directamente.
                            </p>

                            <a
                                :href="`mailto:${contactEmail}`"
                                class="contact-link"
                            >
                                {{ contactEmail }}
                            </a>

                        </div>

                    </div>


                    <!-- TELÉFONO -->

                    <div class="col-md-6 col-lg-3">

                        <a
                            :href="`tel:${phoneNumber}`"
                            class="contact-option d-block text-decoration-none h-100"
                        >

                            <div class="contact-option-icon">
                                <i class="bi bi-telephone"></i>
                            </div>

                            <h3 class="h5 fw-bold mt-4 text-dark">
                                Teléfono
                            </h3>

                            <p class="text-muted mb-2">
                                Llámanos directamente.
                            </p>

                            <span class="contact-link">
                                {{ phoneNumber }}
                            </span>

                        </a>

                    </div>


                    <!-- HORARIO -->

                    <div class="col-md-6 col-lg-3">

                        <div class="contact-option h-100">

                            <div class="contact-option-icon">
                                <i class="bi bi-clock"></i>
                            </div>

                            <h3 class="h5 fw-bold mt-4">
                                Horario
                            </h3>

                            <p class="text-muted mb-1">
                                Lunes a sábado
                            </p>

                            <strong>
                                9:00 AM - 7:00 PM
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             FORMULARIO
        ========================================================== -->

        <section class="form-section py-5 bg-light">

            <div class="container py-5">

                <div class="row align-items-center g-5">


                    <!-- INFORMACIÓN -->

                    <div class="col-lg-5">

                        <span class="text-primary fw-semibold">
                            ENVÍANOS UN MENSAJE
                        </span>

                        <h2 class="display-6 fw-bold mt-2">
                            Cuéntanos qué necesitas
                        </h2>

                        <p class="text-muted mt-3">
                            Completa el formulario con los detalles de tu proyecto.
                            Te responderemos directamente al correo que nos
                            proporciones.
                        </p>


                        <div class="mt-4">


                            <div class="d-flex mb-4">

                                <div class="form-info-icon me-3">
                                    <i class="bi bi-lightbulb"></i>
                                </div>

                                <div>

                                    <strong>
                                        Cuéntanos tu idea
                                    </strong>

                                    <p class="text-muted mb-0">
                                        Explícanos qué necesitas y qué tienes
                                        pensado para tu proyecto.
                                    </p>

                                </div>

                            </div>


                            <div class="d-flex mb-4">

                                <div class="form-info-icon me-3">
                                    <i class="bi bi-palette"></i>
                                </div>

                                <div>

                                    <strong>
                                        Selecciona un servicio
                                    </strong>

                                    <p class="text-muted mb-0">
                                        Indícanos qué tipo de trabajo necesitas.
                                    </p>

                                </div>

                            </div>


                            <div class="d-flex">

                                <div class="form-info-icon me-3">
                                    <i class="bi bi-chat-dots"></i>
                                </div>

                                <div>

                                    <strong>
                                        Nos pondremos en contacto
                                    </strong>

                                    <p class="text-muted mb-0">
                                        Revisaremos tu solicitud y te responderemos.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- FORMULARIO -->

                    <div class="col-lg-7">

                        <div class="contact-form-card p-4 p-lg-5 rounded-4">


                            <!-- SUCCESS -->

                            <div
                                v-if="successMessage"
                                class="alert alert-success"
                            >

                                <i class="bi bi-check-circle me-2"></i>

                                {{ successMessage }}

                            </div>


                            <!-- ERROR -->

                            <div
                                v-if="errorMessage"
                                class="alert alert-danger"
                            >

                                <i class="bi bi-exclamation-circle me-2"></i>

                                {{ errorMessage }}

                            </div>


                            <form @submit.prevent="submitForm">

                                <div class="row g-3">


                                    <!-- NOMBRE -->

                                    <div class="col-md-6">

                                        <label
                                            for="name"
                                            class="form-label fw-semibold"
                                        >
                                            Nombre
                                        </label>

                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="form-control form-control-lg"
                                            placeholder="Tu nombre"
                                            required
                                        >

                                    </div>


                                    <!-- TELÉFONO -->

                                    <div class="col-md-6">

                                        <label
                                            for="phone"
                                            class="form-label fw-semibold"
                                        >
                                            Teléfono
                                        </label>

                                        <input
                                            id="phone"
                                            v-model="form.phone"
                                            type="tel"
                                            class="form-control form-control-lg"
                                            placeholder="Tu número"
                                            required
                                        >

                                    </div>


                                    <!-- EMAIL -->

                                    <div class="col-md-6">

                                        <label
                                            for="email"
                                            class="form-label fw-semibold"
                                        >
                                            Correo electrónico
                                        </label>

                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="form-control form-control-lg"
                                            placeholder="correo@ejemplo.com"
                                            required
                                        >

                                    </div>


                                    <!-- SERVICIO -->

                                    <div class="col-md-6">

                                        <label
                                            for="service"
                                            class="form-label fw-semibold"
                                        >
                                            Servicio
                                        </label>

                                        <select
                                            id="service"
                                            v-model="form.service"
                                            class="form-select form-select-lg"
                                            required
                                        >

                                            <option
                                                value=""
                                                disabled
                                            >
                                                Selecciona un servicio
                                            </option>

                                            <option
                                                v-for="service in services"
                                                :key="service"
                                                :value="service"
                                            >
                                                {{ service }}
                                            </option>

                                        </select>

                                    </div>


                                    <!-- MENSAJE -->

                                    <div class="col-12">

                                        <label
                                            for="message"
                                            class="form-label fw-semibold"
                                        >
                                            Mensaje
                                        </label>

                                        <textarea
                                            id="message"
                                            v-model="form.message"
                                            class="form-control"
                                            rows="6"
                                            placeholder="Cuéntanos sobre tu proyecto..."
                                            required
                                        ></textarea>

                                    </div>


                                    <!-- BOTÓN -->

                                    <div class="col-12 mt-4">

                                        <button
                                            type="submit"
                                            class="btn btn-dark btn-lg w-100 py-3"
                                            :disabled="sending"
                                        >

                                            <span v-if="sending">

                                                <span
                                                    class="spinner-border spinner-border-sm me-2"
                                                ></span>

                                                Enviando...

                                            </span>

                                            <span v-else>

                                                Enviar consulta

                                                <i
                                                    class="bi bi-arrow-right ms-2"
                                                ></i>

                                            </span>

                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             ENCUÉNTRANOS
        ========================================================== -->

        <section class="location-section py-5 bg-light">

            <div class="container py-5">

                <div class="row align-items-center g-5">


                    <!-- INFORMACIÓN -->

                    <div class="col-lg-5">

                        <span class="text-primary fw-semibold">
                            ENCUÉNTRANOS
                        </span>

                        <h2 class="display-6 fw-bold mt-2">
                            Estamos en Cajamarca
                        </h2>

                        <p class="text-muted mt-3">
                            Si prefieres visitarnos personalmente, estaremos
                            encantados de atenderte y conversar sobre tu proyecto.
                        </p>


                        <div class="d-flex align-items-start mt-4">

                            <div class="location-icon me-3">
                                <i class="bi bi-geo-alt"></i>
                            </div>

                            <div>

                                <strong>
                                    Gato Negro
                                </strong>

                                <div class="text-muted">
                                    Cajamarca, Perú
                                </div>

                            </div>

                        </div>


                        <div class="d-flex align-items-start mt-4">

                            <div class="location-icon me-3">
                                <i class="bi bi-clock"></i>
                            </div>

                            <div>

                                <strong>
                                    Horario de atención
                                </strong>

                                <div class="text-muted">
                                    Lunes a sábado
                                </div>

                                <div class="text-muted">
                                    9:00 AM - 7:00 PM
                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- GOOGLE MAPS -->

                    <div class="col-lg-7">

                        <div class="map-container rounded-4 overflow-hidden">

                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.7366081810865!2d-78.51150488890221!3d-7.1564209701824995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b25bc0b0f04619%3A0x896266b32080ebb3!2sGato%20Negro!5e0!3m2!1ses-419!2spe!4v1787240222986!5m2!1ses-419!2spe://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.7366081810865!2d-78.51150488890221!3d-7.1564209701824995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b25bc0b0f04619%3A0x896266b32080ebb3!2sGato%20Negro!5e0!3m2!1ses-419!2spe!4v1787240222986!5m2!1ses-419!2spe"
                                width="100%"
                                height="400"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>

                            <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.7366081810865!2d-78.51150488890221!3d-7.1564209701824995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b25bc0b0f04619%3A0x896266b32080ebb3!2sGato%20Negro!5e0!3m2!1ses-419!2spe!4v1787240222986!5m2!1ses-419!2spe" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe> -->

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             CONTACTO DIRECTO
        ========================================================== -->

        <section class="py-5">

            <div class="container py-5">

                <div class="row justify-content-center text-center">

                    <div class="col-lg-8">

                        <span class="text-primary fw-semibold">
                            ¿PREFIERES CONTACTARNOS DIRECTAMENTE?
                        </span>

                        <h2 class="display-6 fw-bold mt-2">
                            Estamos a un mensaje de distancia
                        </h2>

                        <p class="text-muted mt-3 mb-4">
                            Elige el medio que te resulte más cómodo.
                        </p>


                        <div
                            class="d-flex justify-content-center gap-3 flex-wrap"
                        >

                            <button
                                type="button"
                                class="btn btn-dark btn-lg px-4"
                                @click="openWhatsApp"
                            >

                                <i class="bi bi-whatsapp me-2"></i>

                                WhatsApp

                            </button>


                            <a
                                :href="`tel:${phoneNumber}`"
                                class="btn btn-outline-dark btn-lg px-4"
                            >

                                <i class="bi bi-telephone me-2"></i>

                                Llamar

                            </a>


                            <a
                                :href="`mailto:${contactEmail}`"
                                class="btn btn-outline-dark btn-lg px-4"
                            >

                                <i class="bi bi-envelope me-2"></i>

                                Correo

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             CTA
        ========================================================== -->

        <section class="py-5">

            <div class="container py-4">

                <div class="cta-section rounded-4 p-5 text-center">

                    <span class="text-uppercase small fw-semibold">
                        Gato Negro
                    </span>

                    <h2 class="display-6 fw-bold mt-2 mb-3">
                        Hagamos realidad tu próxima idea
                    </h2>

                    <p class="mb-4">
                        Diseño, publicidad e impresión para hacer destacar tu negocio.
                    </p>

                    <button
                        type="button"
                        class="btn btn-light btn-lg px-5"
                        @click="goToContact"
                    >

                        Conoce más de nosotros

                    </button>

                </div>

            </div>

        </section>

    </main>
</template>


<style scoped>

.contact-page {
    overflow: hidden;
}


/* =========================================================
   HERO
========================================================= */

.contact-hero {
    min-height: 500px;
    display: flex;
    align-items: center;
}


/* =========================================================
   CONTACT OPTIONS
========================================================= */

.contact-option {
    border: 1px solid #e9e9e9;
    border-radius: 20px;
    padding: 30px 25px;
    background: white;
    color: inherit;
    text-align: center;
    transition: all 0.25s ease;
}

button.contact-option {
    cursor: pointer;
}

.contact-option:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
}

.contact-option-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto;

    border-radius: 16px;
    background: var(--bs-primary);
    color: white;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1.5rem;
}

.contact-option-icon.whatsapp {
    background: #212529;
}

.contact-link {
    color: var(--bs-primary);
    font-weight: 600;
}


/* =========================================================
   FORM
========================================================= */

.form-section {
    border-top: 1px solid #eeeeee;
    border-bottom: 1px solid #eeeeee;
}

.contact-form-card {
    background: white;
    border: 1px solid #e9e9e9;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.07);
}

.form-control,
.form-select {
    border-radius: 10px;
    border-color: #dedede;
}

.form-control {
    padding: 14px 15px;
}

.form-control:focus,
.form-select:focus {
    box-shadow: none;
    border-color: var(--bs-primary);
}

.form-info-icon {
    width: 35px;
    height: 35px;
    min-width: 35px;

    border-radius: 50%;
    background: var(--bs-primary);
    color: white;

    display: flex;
    align-items: center;
    justify-content: center;
}


/* =========================================================
   LOCATION
========================================================= */

.location-section {
    border-bottom: 1px solid #eeeeee;
}

.location-icon {
    width: 50px;
    height: 50px;
    min-width: 50px;

    border-radius: 14px;
    background: var(--bs-primary);
    color: white;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1.3rem;
}

.map-container {
    background: #e9e9e9;
    min-height: 400px;
}


/* =========================================================
   CTA
========================================================= */

.cta-section {
    background: #212529;
    color: white;
}

.cta-section p {
    color: rgba(255, 255, 255, 0.75);
}

</style>