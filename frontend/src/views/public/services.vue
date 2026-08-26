<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">

        <!-- Card principal -->
        <div class="card main-card border-0 shadow-lg rounded-4 p-4 p-md-5">

            <!-- Encabezado -->
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-3">
                    Nuestros servicios
                </h1>

                <p class="text-muted mb-0">
                    Conoce los servicios que tenemos disponibles para ti.
                </p>
            </div>

            <!-- BUSCADOR -->
            <div class="row justify-content-center mb-5">

                <div class="col-12 col-md-8 col-lg-6">

                    <div class="input-group input-group-lg shadow-sm">

                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>

                        <input v-model="searchTerm" type="text" class="form-control border-start-0"
                            placeholder="Buscar servicio...">

                    </div>

                </div>

            </div>
            <!-- Grid de servicios -->
            <div class="row g-4">

                <div v-for="service in visibleServices" :key="service.id" class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow rounded-4 overflow-hidden service-card">

                        <img v-if="service.service_image" :src="service.service_image" :alt="service.name"
                            class="card-img-top service-image">

                        <div class="card-body d-flex flex-column p-4">

                            <h5 class="card-title fw-bold">
                                {{ service.name }}
                            </h5>

                            <p class="card-text text-muted">
                                {{ service.description }}
                            </p>

                            <div class="mt-auto">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">
                                        Precio
                                    </span>

                                    <span class="fw-bold fs-5">
                                        S/ {{ Number(service.price).toFixed(2) }}
                                    </span>
                                </div>

                                <button class="btn btn-primary w-100 rounded-pill"
                                    @click="goToServiceDetail(service.id)">
                                    Ver detalles
                                </button>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- SIN RESULTADOS -->
            <div v-if="filteredServices.length === 0 && !cargando" class="text-center py-5">
                <i class="bi bi-search display-5 text-muted"></i>

                <h5 class="mt-3 fw-bold">
                    No encontramos servicios
                </h5>

                <p class="text-muted mb-0">
                    No hay servicios que coincidan con
                    "<strong>{{ searchTerm }}</strong>".
                </p>
            </div>

            <!-- SENTINEL para el infinite scroll -->
            <div ref="sentinel" class="w-100" style="height: 1px;"></div>

            <!-- Loader al cargar más -->
            <div v-if="loadingMore" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

        </div>

    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue';
import ServicesService from '@/services/ServicesService'
import { ref, onMounted, onBeforeUnmount, computed, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router';

const services = ref([])
const router = useRouter();
const cargando = ref(false);
const searchTerm = ref('')

// --- Infinite scroll ---
const PAGE_SIZE = 8
const visibleCount = ref(PAGE_SIZE)
const loadingMore = ref(false)
const sentinel = ref(null)
let observer = null

const getServices = async () => {
    try {
        cargando.value = true;
        const response = await ServicesService.getPublicServices();

        services.value = response.data.data.filter(
            service => service.status === 'active'
        );
    } catch (error) {
        console.error('Error al obtener los servicios:', error)
    } finally {
        cargando.value = false;
        await nextTick()
        setupObserver()
    }
}

const goToServiceDetail = (serviceId) => {
    router.push({ name: 'publicServiceDetails', params: { idservice: serviceId } })
}

const filteredServices = computed(() => {

    const search = searchTerm.value
        .trim()
        .toLowerCase()

    if (!search) {
        return services.value
    }

    return services.value.filter(service =>
        service.name
            .toLowerCase()
            .includes(search)
    )
})

// Solo mostramos un "slice" de los servicios filtrados
const visibleServices = computed(() => {
    return filteredServices.value.slice(0, visibleCount.value)
})

const hasMore = computed(() => visibleCount.value < filteredServices.value.length)

const loadMore = () => {
    if (!hasMore.value || loadingMore.value) return

    loadingMore.value = true

    // Simula una pequeña carga progresiva (si luego paginas desde el backend,
    // aquí es donde harías la petición con page/limit)
    setTimeout(() => {
        visibleCount.value += PAGE_SIZE
        loadingMore.value = false
    }, 400)
}

const setupObserver = () => {
    if (observer) observer.disconnect()
    if (!sentinel.value) return

    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadMore()
            }
        })
    }, {
        root: null,
        rootMargin: '200px',
        threshold: 0
    })

    observer.observe(sentinel.value)
}

// Si el usuario busca, reiniciamos la cantidad visible
watch(searchTerm, () => {
    visibleCount.value = PAGE_SIZE
})

onMounted(() => {
    getServices()
})

onBeforeUnmount(() => {
    if (observer) observer.disconnect()
})
</script>

<style scoped>
.service-image {
    height: 220px;
    object-fit: cover;
}

.service-card {
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
}
</style>