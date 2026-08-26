<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex align-items-center flex-wrap justify-content-between">
                <h1 class="card-title fw-bold">Lista de servicios</h1>
                <button v-if="auth.hasPermission('services.create')" type="button" class="btn btn-primary m-md-3" @click="newService">
                    <i class="bi bi-plus"></i>
                    Nuevo</button>
            </div>
            <div v-if="!cargando && services.length === 0" class="text-center py-5">
                <i class="bi bi-x-circle display-4 text-danger"></i>
                <h4 class="mt-3 fst-italic text-muted">No existen servicios para mostrar</h4>
            </div>
            <div v-else class="table-responsive p-1">
                <DataTable :data="services" :columns="columns" :show-all-option="false">
                    <template #column-0="props">
                        <span>
                            {{ props.rowData.name }}
                        </span>
                    </template>
                    <template #column-1="props">
                        <span>
                            {{ props.rowData.description }}
                        </span>
                    </template>
                    <template #column-2="props">
                        <span>
                            {{ props.rowData.price }}
                        </span>
                    </template>
                    <template #column-3="props">
                        <span>
                            <button v-if="auth.hasPermission('services.change_status')" type="button" class="btn btn-sm"
                                :class="props.rowData.status === 'active' ? 'btn-success' : 'btn-danger'"
                                @click="changeServiceStatus(props.rowData.id)">
                                <i v-if="props.rowData.status === 'active'" class="bi bi-check-circle me-1"></i>
                                <i v-else class="bi bi-x-circle me-1"></i>
                                <span class="d-none d-lg-inline ms-1">
                                    {{ props.rowData.status==='active'?'Activo':'Inactivo' }}
                                </span>
                            </button>
                            <span v-else>{{ props.rowData.status==='active'?'Activo':'Inactivo' }}</span>
                        </span>
                    </template>
                    <template #column-4="props">
                        <span class="d-flex gap-1 justify-content-center">
                            <button v-if="auth.hasPermission('services.view')" type="button" class="btn btn-sm btn-warning"
                                @click="goToServiceDetail(props.rowData.id)">
                                <i class="bi bi-eye"></i>
                                <span class="d-none d-sm-inline">
                                    Detalles
                                </span>
                            </button>
                            <button v-if="auth.hasPermission('services.delete')" type="button" class="btn btn-sm btn-danger"
                                    @click="deleteService(props.rowData.id)">
                                    <i class="bi bi-trash3"></i>
                                    <span class="d-none d-sm-inline">
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
import ServicesService from '@/services/ServicesService';
import { useAuthStore } from '@/stores/auth';
import { confirmAction, showError, showSuccess } from '@/utils/sweetAlert';
import { ref, onMounted, } from 'vue';
import { useRouter } from 'vue-router';

// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);
const services = ref([]);
const auth=useAuthStore();

onMounted(() => {
    getServices();
})

const getServices = async () => {
    try {
        cargando.value = true;
        const response = await ServicesService.getServices();
        services.value = response.data.data;
    } catch (error) {
        
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
    { data: 'description', title: 'Descripción', className: 'd-none d-lg-table-cell text-center' },
    { data: 'price', title: 'Precio', className: 'text-center' },
    { data: 'status', title: 'Estado', className: 'text-center' },
    { data: '', title: 'Acciones', className: 'text-center' }
]

const newService = () => {
    router.push({ name: 'newService' });
}

const goToServiceDetail = async (serviceId) => {
    router.push({ name: 'serviceDetails', params: { idservice: serviceId } });
}

const changeServiceStatus = async (userId) => {
    const result=await confirmAction('Se modificara el estado del servicio')

    if(!result.isConfirmed){
        return
    }
    try {
        await ServicesService.patchServiceStatus(userId);
        await showSuccess('Se modificó el estado del servicio correctamente')
        await getServices();
    } catch (err) {
        await showError('No se pudo modificar el estado del servicio')
    }
}

const deleteService = async (serviceId) => {
    const result = await confirmAction('Se eliminara el servicio');
    if (!result.isConfirmed) {
        return
    }
    try {
        await ServicesService.deleteService(serviceId);
        await showSuccess('Servicio eliminado correctamente')
        getServices();
    } catch (err) {
        await showError('No se puede eliminar el servicio porque cuenta con transacciones vinculadas')
    }

}
</script>
