<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">

        <!-- Card principal -->
        <div class="card main-card border-0 shadow-lg rounded-4 p-4 p-md-5">

            <!-- Encabezado -->
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-3">
                    Nuestras promociones
                </h1>

                <p class="text-muted mb-0">
                    Conoce las promociones vigentes.
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
                            placeholder="Buscar promoción...">

                    </div>

                </div>

            </div>

            <!-- Grid de promociones -->
            <div class="row g-4">

                <div v-for="promotion in visiblePromotions" :key="promotion.id" class="col-12 col-md-6 col-lg-3 ">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden promotion-card">

                        <img v-if="promotion.promotion_image" :src="promotion.promotion_image" :alt="promotion.name"
                            class="card-img-top promotion-image">

                        <div class="card-body d-flex flex-column p-4">

                            <!-- Nombre -->
                            <h5 class="card-title fw-bold">
                                {{ promotion.name }}
                            </h5>

                            <!-- Descripción -->
                            <p v-if="promotion.description" class="card-text text-muted">
                                {{ promotion.description }}
                            </p>

                            <div class="mt-auto">

                                <!-- Descuento -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">
                                        Descuento
                                    </span>

                                    <span class="fw-bold fs-5">
                                        <template v-if="promotion.discount_type === 'percentage'">
                                            {{ Number(promotion.discount_value).toFixed(0) }} %
                                        </template>

                                        <template v-else-if="promotion.discount_type === 'fixed'">
                                            S/. {{ Number(promotion.discount_value).toFixed(2) }}
                                        </template>
                                    </span>
                                </div>

                                <!-- Vigencia -->
                                <div class="small text-muted mb-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Desde:</strong>
                                            {{ formatDate(promotion.start_date) }}
                                        </div>
                                        <div>
                                            <strong>Hasta:</strong>
                                            {{ formatDate(promotion.end_date) }}
                                        </div>
                                    </div>

                                </div>

                                <!-- Botón -->
                                <button class="btn btn-primary w-100 rounded-pill"
                                    @click="goToPromotionDetail(promotion.id)">
                                    Ver detalles
                                </button>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- SIN RESULTADOS -->
            <div v-if="filteredPromotions.length === 0 && !cargando" class="text-center py-5">
                <i class="bi bi-search display-5 text-muted"></i>

                <h5 class="mt-3 fw-bold">
                    No encontramos promociones.
                </h5>

                <p class="text-muted mb-0">
                    No hay promociones que coincidan con
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
import PromotionService from '@/services/PromotionService';
import { ref, onMounted, onBeforeUnmount, computed, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router';

const promotions = ref([])
const router = useRouter();
const cargando = ref(false);
const searchTerm = ref('')

// --- Infinite scroll ---
const PAGE_SIZE = 8
const visibleCount = ref(PAGE_SIZE)
const loadingMore = ref(false)
const sentinel = ref(null)
let observer = null

const formatDate = (date) => {
    if (!date) return ''

    const [year, month, day] = date.split('-')

    return `${day}/${month}/${year}`
}

const getPromotionss = async () => {
    try {
        cargando.value = true;
        const response = await PromotionService.getPublicPromotions();


        promotions.value = response.data.data.filter(
            promotion => promotion.status === 'active'
        );
    } catch (error) {
        console.error('Error al obtener las promociones:', error)
    } finally {
        cargando.value = false;
        await nextTick()
        setupObserver()
    }
}

const goToPromotionDetail = (promotionId) => {
    router.push({ name: 'publicPromotionDetails', params: { idpromotion: promotionId } })
}

const filteredPromotions = computed(() => {

    const search = searchTerm.value
        .trim()
        .toLowerCase()

    if (!search) {
        return promotions.value
    }

    return promotions.value.filter(promotion =>
        promotion.name
            .toLowerCase()
            .includes(search)
    )
})

// Solo mostramos un "slice" de las promociones filtradas
const visiblePromotions = computed(() => {
    return filteredPromotions.value.slice(0, visibleCount.value)
})

const hasMore = computed(() => visibleCount.value < filteredPromotions.value.length)

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
    getPromotionss()
})

onBeforeUnmount(() => {
    if (observer) observer.disconnect()
})
</script>

<style scoped>
.promotion-image {
    height: 220px;
    object-fit: cover;
}

.promotion-card {
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.promotion-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
}
</style>