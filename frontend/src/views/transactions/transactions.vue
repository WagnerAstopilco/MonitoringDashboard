<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex align-items-center flex-wrap justify-content-between">
                <h1 class="card-title fw-bold">Historial de transacciones</h1>
                <button v-if="auth.hasPermission('transactions.create')" type="button" class="btn btn-primary m-md-3" @click="newTransaction">
                    <i class="bi bi-plus"></i>
                    Nuevo</button>
            </div>
            <div v-if="!cargando && transactions.length === 0" class="text-center py-5">
                <i class="bi bi-x-circle display-4 text-danger"></i>
                <h4 class="mt-3 fst-italic text-muted">No existen transacciones para mostrar</h4>
            </div>
            <div v-else class="table-responsive">
                <DataTable :data="transactions" :columns="columns" :show-all-option="false">
                    <template #column-0="props">
                        <span>
                            {{ props.rowData.transaction_date }}
                        </span>
                    </template>
                    <template #column-1="props">
                        <span>
                            {{ props.rowData.transaction_type==='income'?'Ingreso':'Egreso' }}
                        </span>
                    </template>
                    <template #column-2="props">
                        <span>

                            {{ props.rowData.user?.username }}
                        </span>
                    </template>
                    <template #column-3="props">
                        <span>
                            {{ props.rowData.amount }}
                        </span>
                    </template>
                    <template #column-4="props">
                        <span>
                            {{ props.rowData.status }}
                        </span>
                    </template>
                    <template #column-5="props">
                        <button type="button" class="btn btn-sm btn-warning"
                            @click="goToTransactionDetails(props.rowData.id)">
                            <i class="bi bi-eye"></i>
                            <span class="d-none d-lg-inline ms-1">
                                Ver detalles
                            </span>
                        </button>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import { ref, onMounted, } from 'vue';
import { useRouter } from 'vue-router';
import DataTable from '@/components/tables/dataTable.vue';
import TransactionService from '@/services/transactionService';
import { useAuthStore } from '@/stores/auth';

// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);
const transactions = ref([]);
const auth=useAuthStore();

onMounted(() => {
    getTransactions();
})

const getTransactions = async () => {
    try {
        cargando.value = true;
        const response = await TransactionService.getTransactions();
        transactions.value = response.data.data;
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
    { data: 'transaction_date', title: 'Fecha', className: 'text-center' },
    { data: 'transaction_type', title: 'Tipo', className: 'd-none d-md-table-cell text-center' },
    { data: 'user', title: 'Usuario', className: 'text-center' },
    { data: 'amount', title: 'Monto', className: 'text-center' },
    { data: 'status', title: 'Estado', className: 'd-none d-md-table-cell text-center' },
    { data: '', title: 'Acciones', className: 'text-center' }

]

const newTransaction = () => {
    router.push({ name: 'newTransaction' })
}

const goToTransactionDetails = (idtransaction) => {
    router.push({ name: 'transactionDetails', params: { idtransaction: idtransaction } })
}
</script>
