<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
            <!-- header -->
            <div class="d-flex flex-column flex-md-row justify-content-between
                        align-items-md-center mb-4">
                <div>
                    <h1 class="card-title fw-bold">Entregas pendientes</h1>
                    <p class="text-muted">Transacciones pendientes de entrega</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-dark fs-6" disabled>
                        {{ transactions.length }} pendientes
                    </button>
                    <button v-if="auth.hasPermission('transactions.create')" type="button" class="btn btn-primary fs-6" @click="newTransaction">
                        <i class="bi bi-plus"></i>
                        Nuevo</button>
                </div>
            </div>

            <!-- POST-ITS -->
            <div v-if="!cargando && transactions.length === 0" class="text-center py-5">
                <i class="bi bi-check-circle display-4 text-success"></i>
                <h4 class="mt-3">No hay entregas pendientes</h4>
                <p class="text-muted">Todas las transacciones han sido entregadas.</p>
            </div>

            <div v-else class="row g-4">
                <div v-for="transaction in transactions" :key="transaction.id"
                    class="col-12 col-md-6 col-xl-4 col-xxl-3">
                    <div class="h-100 p-4 rounded-3 shadow post-it d-flex flex-column"
                        :class="getPostItClass(transaction)">
                        <!-- client -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="mb-3">
                                <h4 class="fw-bold mb-0">
                                    {{ transaction.client?.name }}
                                </h4>
                                <small class="text-muted">{{ transaction.client?.company_name }}</small>
                            </div>
                            <button v-if="auth.hasPermission('transactions.change_delivery_status')" type="button" class="btn btn-sm btn-outline-dark rounded-circle"
                                :disabled="transaction.status !== 'paid'" title="Marcar como entregado"
                                @click="deliverTransaction(transaction)">
                                <i class="bi bi-box-seam"></i>
                            </button>
                        </div>
                        <!-- content -->
                        <div class="flex-grow-1 mb-4">
                            <!-- FECHA ENTREGA/STATUS -->
                            <div class="d-flex flex-wrap justify-content-between mb-2 pb-2 border-dashed gap-2">
                                <div>
                                    <small class="text-muted d-block  mb-1">Fecha de entrega</small>
                                    <strong>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ formatDate(transaction.delivery_date) }}
                                    </strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Estado del pago</small>
                                    <span class="badge" :class="getStatusClass(transaction.status)">
                                        {{ getStatusText(transaction.status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- MONTOS -->
                            <div>
                                <div class="d-flex flex-wrap justify-content-between">
                                    <span>Total</span>
                                    <strong>S/ {{ formatMoney(transaction.amount) }}</strong>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between">
                                    <span>Pagado</span>
                                    <strong class="text-success">S/ {{ formatMoney(getPaidAmount(transaction))
                                    }}</strong>
                                </div>
                                <div v-if="transaction.status !== 'paid'"
                                    class="d-flex flex-wrap justify-content-between align-items-center mt-2">
                                    <span>Pendiente</span>
                                    <strong class="text-danger fs-5">S/ {{ formatMoney(getPendingAmount(transaction))
                                    }}</strong>
                                </div>

                            </div>
                        </div>
                        <!-- butons -->
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <!-- Button trigger pay modal -->
                            <button v-if="transaction.status !== 'paid' && auth.hasPermission('payments.create')" type="button" class="btn btn-dark"
                                data-bs-toggle="modal" data-bs-target="#payModal"
                                @click="openPaymentModal(transaction)">
                                <i class="bi bi-cash-coin me-2"></i>
                                Pagar
                            </button>

                            <!-- Button trigger details modal -->
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                data-bs-target="#transactionDetailsModal"@click="openTransactionDetailsModal(transaction)">
                                <i class="bi bi-eye"></i>
                                Detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- pay Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold mb-0">Registrar pago</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedTransaction">
                        <div class="mb-3">
                            <strong>
                                {{ selectedTransaction.client?.name }}
                                {{ selectedTransaction.client?.last_name }}
                            </strong>
                            <div class="text-muted small">
                                Transacción #{{ selectedTransaction.id }}
                            </div>
                        </div>

                        <div class="alert alert-light border">
                            <div class="d-flex justify-content-between">
                                <span>Total</span>
                                <strong>
                                    S/ {{ formatMoney(selectedTransaction.amount) }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Pagado</span>
                                <strong class="text-success">
                                    S/ {{ formatMoney(getPaidAmount(selectedTransaction)) }}
                                </strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Falta</span>
                                <strong class="text-danger">
                                    S/ {{ formatMoney(
                                        getPendingAmount(selectedTransaction)
                                    ) }}
                                </strong>
                            </div>
                        </div>

                        <!-- MONTO -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Monto a pagar
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    S/
                                </span>
                                <input v-model.number="paymentForm.amount" type="number" min="0.01" step="0.01"
                                    class="form-control" :max="getPendingAmount(selectedTransaction)">
                            </div>
                        </div>

                        <!-- MÉTODO DE PAGO -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Método de pago
                            </label>
                            <select v-model="paymentForm.payment_method_id" class="form-select">
                                <option value="">
                                    Seleccionar método
                                </option>
                                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                                    {{ method.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="registerPayment">
                        <span v-if="savingPayment" class="spinner-border spinner-border-sm me-2"></span>
                        Registrar pago</button>
                </div>
            </div>
        </div>
    </div>

    <!-- transaction details Modal -->
    <div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="transactionDetailsModalLabel">Detalles de la Transacción</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedTransaction">

                <div class="mb-3">

                    <strong>
                        {{ selectedTransaction.client?.name }}
                        {{ selectedTransaction.client?.last_name }}
                    </strong>

                    <div class="text-muted small">
                        Transacción #{{ selectedTransaction.id }}
                    </div>

                </div>

                <!-- datos del cliente -->
                <div class="alert alert-light border">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Razón social</label>
                            <input class="form-control" :value="selectedTransaction.client.company_name" disabled />
                        </div>
                        <div class="form-group col-md-6">
                            <label>Teléfono</label>
                            <input class="form-control" :value="selectedTransaction.client.phone" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" :value="selectedTransaction.client.email" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input class="form-control" :value="selectedTransaction.client.address" disabled />
                        </div>
                    </div>
                </div>
                <!-- datos de la promocion -->
                <div class="alert alert-light border" v-if="selectedTransaction.promotion">
                    <div class="d-flex justify-content-between align-items-center w-100 border rounded p-2">
                        <span>{{ selectedTransaction.promotion.name }}</span>
                        <strong class="text-danger">
                            S/ -{{ Number(selectedTransaction.promotion.discount_value).toFixed(2) }}
                        </strong>
                    </div>
                </div>

                <!-- servicios adquiridos -->
                <div class="alert alert-light border">
                    <h5>Servicios adquiridos</h5>
                    <table class="table table-bordered mt-2">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th style="width: 140px;">Precio</th>
                                <th style="width: 140px;">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in selectedTransaction.transactionDetails" :key="item.id">
                                <td>{{ item.service?.name ?? item.service_name ?? '—' }}</td>
                                <td>{{ item.unit_price }}</td>
                                <td>{{ item.quantity }}</td>
                            </tr>
                        </tbody>
                        </table>
                </div>
                </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import Preloader from '@/components/layout/preloader.vue'
import PaymentMethodService from '@/services/PaymentMethodService';
import TransactionService from '@/services/transactionService';
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router'
import { Modal } from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { confirmAction, showError, showSuccess } from '@/utils/sweetAlert';
import { useAuthStore } from '@/stores/auth'

const auth=useAuthStore();
const cargando = ref(false);
const transactions = ref([]);
const paymentMethods = ref([]);

const selectedTransaction = ref(null);

const savingPayment = ref(false);

const router = useRouter()


const paymentForm = ref({
    amount: 0,
    payment_method_id: ''
});


// ==========================================
// CARGAR TRANSACCIONES
// ==========================================

const getTransactions = async () => {

    cargando.value = true;

    try {

        const response = await TransactionService.getTransactions();

        const data = response.data.data.filter(
            transaction => transaction.transaction_type === 'income');

        transactions.value = data
            .filter(transaction => !transaction.delivery_status)
            .sort((a, b) => {

                if (!a.delivery_date) return 1;
                if (!b.delivery_date) return -1;

                return new Date(a.delivery_date) -
                    new Date(b.delivery_date);

            });

    } catch (error) {

        

    } finally {

        cargando.value = false;

    }

};


// ==========================================
// CARGAR MÉTODOS DE PAGO
// ==========================================

const getPaymentMethods = async () => {

        const response = await PaymentMethodService.getPaymentMethods()
        paymentMethods.value =
            response.data.data ?? response.data;

};


// ==========================================
// CALCULAR TOTAL PAGADO
// ==========================================

const getPaidAmount = (transaction) => {
    if (!transaction.transactionPayments) {
        return 0;
    }

    return transaction.transactionPayments.reduce(
        (total, payment) => {

            return total + Number(payment.amount);

        },
        0
    );

};


// ==========================================
// CALCULAR SALDO PENDIENTE
// ==========================================

const getPendingAmount = (transaction) => {

    const total = Number(transaction.amount);

    const paid = getPaidAmount(transaction);

    return Math.max(total - paid, 0);

};


// ==========================================
// ESTADO
// ==========================================

const getStatusText = (status) => {

    const statuses = {
        paid: 'Pagado',
        pending: 'Pendiente',
        partially_paid: 'Pago parcial'
    };

    return statuses[status] ?? status;

};


const getStatusClass = (status) => {

    const classes = {
        paid: 'bg-success',
        pending: 'bg-danger',
        partially_paid: 'bg-warning text-dark'
    };

    return classes[status] ?? 'bg-secondary';

};


const getPostItClass = (transaction) => {

    if (transaction.status === 'paid') {
        return 'post-it-paid';
    }

    if (transaction.status === 'partially_paid') {
        return 'post-it-partial';
    }

    return 'post-it-pending';

};


// ==========================================
// FORMATO FECHA
// ==========================================

const formatDate = (date) => {

    if (!date) {
        return 'Sin fecha';
    }

    return new Date(`${date}T00:00:00`).toLocaleDateString(
        'es-PE',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }
    );

};


// ==========================================
// FORMATO DINERO
// ==========================================

const formatMoney = (amount) => {

    return Number(amount || 0).toFixed(2);

};


// ==========================================
// ABRIR MODAL
// ==========================================

const openPaymentModal = (transaction) => {

    selectedTransaction.value = transaction;

    paymentForm.value = {
        amount: getPendingAmount(transaction),
        payment_method_id: ''
    };

    // showPaymentModal.value = true;

};

const openTransactionDetailsModal = (transaction) => {

    selectedTransaction.value = transaction;


    // showTransactionDetailsModal.value = true;

};

// ==========================================
// REGISTRAR PAGO
// ==========================================

const payment_type = ref(null);

const registerPayment = async () => {


    if (!selectedTransaction.value) {
        return;
    }

    if (
        !paymentForm.value.amount ||
        paymentForm.value.amount <= 0
    ) {
        return;
    }

    if (!paymentForm.value.payment_method_id) {
        return;
    }


    if (
        paymentForm.value.amount >
        getPendingAmount(selectedTransaction.value)
    ) {
        return;
    }


    savingPayment.value = true;


    if (getPendingAmount(selectedTransaction.value) < selectedTransaction.value.amount) {
        if (paymentForm.value.amount < getPendingAmount(selectedTransaction.value)) {
            payment_type.value = 'advance'
        } else {
            payment_type.value = 'balance'
        }
    } else {
        payment_type.value = 'full'
    }

    try {

        await TransactionService.addPayment({

            transaction_id:
                selectedTransaction.value.id,


            amount:
                paymentForm.value.amount,

            payment_method_id:
                paymentForm.value.payment_method_id,
            payment_type: payment_type.value,

        });
        closeModal('payModal')

        await showSuccess('Pago registrado correctamente')
        await getTransactions();

    } catch (error) {

        await showError('No se pudo registrar el pago')

    } finally {

        savingPayment.value = false;

    }

};

const closeModal = (idModal) => {
    const modalEl = document.getElementById(idModal);
    if (!modalEl) return;
    Modal.getInstance(modalEl)?.hide();
};
// ==========================================
// ENTREGAR
// ==========================================

const deliverTransaction = async (transaction) => {

    if (transaction.status !== 'paid') {
        return;
    }

    const result=await confirmAction('Se entregará el pedido')

    if(!result.isConfirmed)
    {
        return
    }
    try {

        await TransactionService.patchDeliveryStatus(transaction.id);
        await showSuccess('Productos entregados correctamente')

        getTransactions();

    } catch (error) {

        await showError('No se pudo entregar los productos')

    }

};

const newTransaction = () => {
    router.push({ name: 'newTransaction' });
}

onMounted(() => {

    getTransactions();

    getPaymentMethods();
    ['payModal', 'transactionDetailsModal'].forEach((id) => {
        const modalEl = document.getElementById(id);
        if (!modalEl) return;

        modalEl.addEventListener('hide.bs.modal', () => {
            if (modalEl.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });

});

</script>


<style scoped>
/* ==========================================
   POST IT
========================================== */
.post-it {
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.post-it:hover {
    transform: translateY(-10px) rotate(0deg);
}

.post-it-paid {
    background: #d9f7df;
}

.post-it-partial {
    background: #fff1b8;
}

.post-it-pending {
    background: #ffd6d6;
}

/* ==========================================
   CONTENIDO
========================================== */
.border-dashed {
    border-bottom: 1px dashed rgba(0, 0, 0, 0.25);
}
</style>