<template>
    <Preloader :visible="cargando"></Preloader> 
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <h1 class="card-title fw-bold">Detalle de transacción</h1>
            <div class="card-body w-lg-80 w-md-90 w-100 mx-auto" v-if="transaction">

                <!-- Sección 1: Datos de la transacción -->
                <h3 class="card-subtitle">Datos de la transacción</h3>
                <div class="row">
                    <div class="form-group p-2 col-md-4">
                        <label>Tipo de transacción</label>
                        <input class="form-control" :value="transactionTypeLabel" disabled />
                    </div>
                    <div class="form-group p-2 col-md-4">
                        <label>Fecha de transacción</label>
                        <input class="form-control" type="date" :value="transaction.transaction_date" disabled />
                    </div>
                    <div class="form-group p-2 col-md-4">
                        <label>Fecha de entrega</label>
                        <input class="form-control" type="date" :value="transaction.delivery_date" disabled />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group p-2 col-md-4">
                        <label>Responsable</label>
                        <input class="form-control" :value="responsibleLabel" disabled />
                    </div>
                    <div class="form-group p-2 col-md-4">
                        <label>Estado</label>
                        <input class="form-control" :value="transaction.status" disabled />
                    </div>
                    <div class="form-group p-2 col-md-4">
                        <label>Estado de entrega</label>
                        <input class="form-control" :value="transaction.delivery_status" disabled />
                    </div>
                </div>

                <!-- Sección 2: Cliente -->
                <div class="mt-3" v-if="transaction.client">
                    <h3 class="card-subtitle">Cliente</h3>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>RUC</label>
                            <input class="form-control" :value="transaction.client.company_ruc" disabled />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Nombre del responsable</label>
                            <input class="form-control" :value="transaction.client.name" disabled />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Razón social</label>
                            <input class="form-control" :value="transaction.client.company_name" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Teléfono</label>
                            <input class="form-control" :value="transaction.client.phone" disabled />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email</label>
                            <input class="form-control" :value="transaction.client.email" disabled />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Dirección</label>
                            <input class="form-control" :value="transaction.client.address" disabled />
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Promoción -->
                <div class="modulePromotions p-2 mt-3" v-if="transaction.promotion">
                    <h3 class="fs-4 d-flex w-100">Promoción</h3>
                    <div class="d-flex justify-content-between align-items-center w-100 border rounded p-2">
                        <span>{{ transaction.promotion.name }}</span>
                        <strong class="text-danger">
                            S/ -{{ Number(transaction.promotion.discount_value).toFixed(2) }}
                        </strong>
                    </div>
                </div>

                <!-- Sección 4: Servicios -->
                <div class="mt-4">
                    <h3 class="card-subtitle">Servicios</h3>
                    <div class="table-responsive" v-if="details.length">
                        <table class="table table-bordered mt-2" >
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th style="width: 140px;">Precio unitario</th>
                                    <th style="width: 140px;">Cantidad</th>
                                    <th style="width: 140px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in details" :key="item.id">
                                    <td>{{ item.service?.name ??'—' }}</td>
                                    <td>{{ formatCurrency(item.unit_price) }}</td>
                                    <td>{{ item.quantity }}</td>
                                    <td>{{ formatCurrency(item.unit_price * item.quantity) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-muted">Esta transacción no tiene servicios registrados.</p>
                    <div class="d-flex justify-content-end">
                        <h6>SubTotal: {{ formatCurrency(subtotal) }}</h6>
                    </div>
                    <div class="d-flex justify-content-end" v-if="transaction.promotion">
                        <h6>Descuento: {{ formatCurrency(discount) }}</h6>
                    </div>
                    <div class="d-flex justify-content-end">
                        <h3>Total: {{ formatCurrency(total) }}</h3>
                    </div>
                </div>

                <!-- Sección 5: Pagos (solo aplica en detalle, no en creación) -->
                <div>
                    <h3 class="card-subtitle">Pagos</h3>
                    <div class="table-responsive" v-if="payments.length">
                        <table class="table table-bordered mt-2" >
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Método de pago</th>
                                    <th>Tipo</th>
                                    <th style="width: 140px;">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in payments" :key="payment.id">
                                    <td>{{ formatDate(payment.created_at) }}</td>
                                    <td>{{ payment.payment_method?.name ?? payment.payment_method_name ?? '—' }}</td>
                                    <td>{{ paymentTypeLabel(payment.payment_type) }}</td>
                                    <td>{{ formatCurrency(payment.amount) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-muted">Esta transacción aún no tiene pagos registrados.</p>
                    <div class="d-flex justify-content-end" v-if="payments.length">
                        <h6>Total pagado: {{ formatCurrency(totalPaid) }}</h6>
                    </div>
                    <div class="d-flex justify-content-end">
                        <h5 :class="balance > 0 ? 'text-danger' : 'text-success'">
                            Saldo pendiente: {{ formatCurrency(balance) }}
                        </h5>
                    </div>

                </div>

                <p v-if="error" style="color: red;">{{ error }}</p>

                <div class="d-flex gap-2 mt-3 justify-content-center">
                    <button type="button" class="btn btn-danger" @click="goBack">Volver</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import TransactionService from '@/services/transactionService'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const cargando = ref(false)
const error = ref('')
const transaction = ref(null)

const details = computed(() => transaction.value?.transactionDetails ?? [])
const payments = computed(() => transaction.value?.transactionPayments ?? [])

// ---------------------------------------------------------------------------
// Etiquetas
// ---------------------------------------------------------------------------
const transactionTypeLabel = computed(() => {
    const map = { income: 'Ingreso', expense: 'Egreso' }
    return map[transaction.value?.transaction_type] ?? transaction.value?.transaction_type
})

const responsibleLabel = computed(() => {
    const map = { edgar: 'Edgar', jorge: 'Jorge' }
    return map[transaction.value?.responsible] ?? transaction.value?.responsible
})

const paymentTypeLabel = (type) => {
    const map = { advance: 'Adelanto', balance: 'Saldo', full: 'Pago completo' }
    return map[type] ?? type
}

// ---------------------------------------------------------------------------
// Totales
// ---------------------------------------------------------------------------
const subtotal = computed(() =>
    details.value.reduce((sum, item) => sum + item.unit_price * item.quantity, 0)
)

const discount = computed(() => {
    if (!transaction.value?.promotion) {
        return 0
    }
    return Math.min(transaction.value.promotion.discount_value, subtotal.value)
})

// El total mostrado usa el "amount" ya calculado y guardado en el backend
// (fuente de verdad). Si no viniera por algún motivo, se calcula localmente.
const total = computed(() =>
    transaction.value?.amount ?? (subtotal.value - discount.value)
)

const totalPaid = computed(() =>
    payments.value.reduce((sum, p) => sum + Number(p.amount), 0)
)

const balance = computed(() => total.value - totalPaid.value)

const formatCurrency = (value) =>
    new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value || 0)

const formatDate = (value) => {
    if (!value) return '—'
    return new Date(value).toLocaleDateString('es-PE')
}

// ---------------------------------------------------------------------------
// Carga de datos
// ---------------------------------------------------------------------------
const getTransaction = async () => {
    try {
        cargando.value = true
        // Ajustar el nombre del método al que exponga tu TransactionService
        // (ej. getTransaction, show, find, etc.)
        const response = await TransactionService.getTransactionDetails(route.params.idtransaction)
        transaction.value = response.data.data ?? response.data
    } catch (err) {
        // error.value = 'No se pudo cargar la transacción'
    } finally {
        cargando.value = false
    }
}

// ---------------------------------------------------------------------------
// Navegación
// ---------------------------------------------------------------------------
const goBack = () => {
    router.push({ name: 'transactions' })
}

onMounted(getTransaction)
</script>