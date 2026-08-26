<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-4 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex align-items-center flex-wrap justify-content-between">
                <h1 class="card-title fw-bold">Lista de promociones</h1>
                <button v-if="auth.hasPermission('promotions.create')" type="button" class="btn btn-primary m-md-3" @click="newPromotion">
                    <i class="bi bi-plus"></i>
                    Nuevo</button>
            </div>
            <div v-if="!cargando && promotions.length === 0" class="text-center py-5">
                <i class="bi bi-x-circle display-4 text-danger"></i>
                <h4 class="mt-3 fst-italic text-muted">Aun no existen promociones para mostrar</h4>
            </div>
            <div v-else class="table-responsive p-1">
                <DataTable :data="promotions" :columns="columns" :show-all-option="false">
                    <template #column-0="props">
                        <span>
                            {{ props.rowData.name }}
                        </span>
                    </template>
                    <template #column-1="props">
                        <span>
                            {{ props.rowData.discount_type }}
                        </span>
                    </template>
                    <template #column-2="props">
                        <span>
                            {{ props.rowData.discount_value }}
                        </span>
                    </template>
                    <template #column-3="props">
                        <span>
                            {{ props.rowData.start_date }}
                        </span>
                    </template>
                    <template #column-4="props">
                        <span>
                            {{ props.rowData.end_date }}
                        </span>
                    </template>
                    <template #column-5="props">
                        <span>
                            <button v-if="auth.hasPermission('promotions.change_status')" type="button" class="btn btn-sm"
                                :class="props.rowData.status === 'active' ? 'btn-success' : 'btn-danger'"
                                @click="changePromotionStatus(props.rowData.id)">
                                <i v-if="props.rowData.status === 'active'" class="bi bi-check-circle me-1"></i>
                                <i v-else class="bi bi-x-circle me-1"></i>
                                <span class="d-none d-lg-inline ms-1">
                                    {{ props.rowData.status==='active'?'Activa':'Inactiva' }}
                                </span>
                            </button>
                            <span v-else>{{ props.rowData.status==='active'?'Activa':'Inactiva' }}</span>
                        </span>
                    </template>
                    <template #column-6="props">
                        <span class="d-flex gap-1 justify-content-center">
                            <button v-if="auth.hasPermission('promotions.view')" type="button" class="btn btn-sm btn-warning"
                                @click="goToPromotionDetail(props.rowData.id)">
                                <i class="bi bi-eye"></i>
                                <span class="d-none d-xl-inline ms-1">
                                    Detalles
                                </span>
                            </button>
                            <button v-if="auth.hasPermission('promotions.delete')" type="button" class="btn btn-sm btn-danger"
                                @click="deletePromotion(props.rowData.id)">
                                <i class="bi bi-trash3"></i>
                                <span class="d-none d-xl-inline ms-1">
                                    Eliminar
                                </span>
                            </button>

                        </span>
                    </template>

                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import DataTable from '@/components/tables/dataTable.vue';
import PromotionService from '@/services/PromotionService';
import { useAuthStore } from '@/stores/auth';
import { confirmAction, showError, showSuccess } from '@/utils/sweetAlert';
import { ref, onMounted, } from 'vue';
import { useRouter } from 'vue-router';

// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);
const promotions = ref([]);
const auth=useAuthStore();
onMounted(() => {
    getPromotions();
})

const getPromotions = async () => {
    try {
        cargando.value = true;
        const response = await PromotionService.getPromotions();
        promotions.value = response.data.data;
    } catch (error) {
        // await showError('No se encontraron promociones disponibles');
    } finally {
        cargando.value = false;
    }
}

// ---------------------------------------------------------------------------
// Section 2: dataTable
// ---------------------------------------------------------------------------
const router = useRouter();
const columns = [
    { data: 'name', title: 'Nombre', className: 'text-center' },
    { data: 'discount_type', title: 'Tipo', className: 'text-center' },
    { data: 'discount_value', title: 'Valor', className: 'd-none d-md-table-cell text-center' },
    { data: 'start_date', title: 'Fecha de inicio', className: 'd-none d-md-table-cell text-center' },
    { data: 'end_date', title: 'Fecha de fin', className: 'd-none d-md-table-cell  text-center' },
    { data: 'status', title: 'Estado', className: 'text-center' },
    { data: '', title: 'Acciones', className: 'text-center' },
]
const newPromotion = () => {
    router.push({ name: 'newPromotion' });
}

const goToPromotionDetail = (promotionId) => {
    router.push({ name: 'promotionDetails', params: { idpromotion: promotionId } });
}

const changePromotionStatus = async (promotionId) => {
    const result = await confirmAction('Se modificata el estado da la promoción')

    if (!result.isConfirmed) {
        return
    }
    try {
        cargando.value = true;
        await PromotionService.patchPromotionStatus(promotionId);
        await getPromotions();
        await showSuccess('Se modifico el estado de la promoción correctamente')
    } catch (err) {
        await showError('No se pudo modificar el estado de la promoción')
    }
    finally {
        cargando.value = false;
    }
}

const deletePromotion = async (promotionId) => {
    const result = await confirmAction('Se eliminara la promoción');
    if (!result.isConfirmed) {
        return
    }
    try {
        await PromotionService.deletePromotion(promotionId);
        await showSuccess('Pomoción eliminada correctamente')
        getPromotions();
    } catch (err) {
        await showError('No se puede eliminar la promoción cuando esta activa')
    }

}
</script>
