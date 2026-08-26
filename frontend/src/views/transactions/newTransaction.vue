<template>
    <Preloader :visible="cargando"></Preloader>

    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <h1 class="card-title fw-bold">Nueva transacción</h1>

            <div class="card-body w-lg-80 w-md-90 w-100">

                <h3 class="card-subtitle">Datos de la transacción</h3>

                <form @submit.prevent>

                    <!-- ===================================================== -->
                    <!-- SECCIÓN 1: DATOS DE LA TRANSACCIÓN -->
                    <!-- ===================================================== -->

                    <div class="row">

                        <!-- Tipo de transacción -->
                        <div class="form-group col-md-6 col-lg-3 p-2">
                            <label for="transaction_type">
                                Tipo de transacción
                            </label>

                            <select
                                id="transaction_type"
                                class="form-select"
                                v-model="transactionForm.transaction_type"
                                :disabled="isEmployee"
                                required
                            >
                                <option value="" disabled>
                                    Selecciona un tipo
                                </option>

                                <option value="income">
                                    Ingreso
                                </option>

                                <option value="expense">
                                    Egreso
                                </option>
                            </select>
                        </div>


                        <!-- Responsable -->
                        <div class="form-group col-md-6 col-lg-3 p-2">
                            <label for="responsible">
                                Responsable
                            </label>

                            <select
                                id="responsible"
                                class="form-select"
                                v-model="transactionForm.responsible"
                                :disabled="isEmployee"
                                required
                            >
                                <option value="" disabled>
                                    Selecciona un responsable
                                </option>

                                <option value="edgar">
                                    Edgar
                                </option>

                                <option value="jorge">
                                    Jorge
                                </option>
                            </select>
                        </div>


                        <!-- Fecha de transacción -->
                        <div class="form-group col-md-6 col-lg-3 p-2">
                            <label for="transaction_date">
                                Fecha de transacción
                            </label>

                            <input
                                id="transaction_date"
                                class="form-control"
                                type="date"
                                v-model="transactionForm.transaction_date"
                                required
                                disabled
                            />
                        </div>


                        <!-- Fecha de entrega -->
                        <div
                            v-if="!isExpense"
                            class="form-group col-md-6 col-lg-3 p-2"
                        >
                            <label for="delivery_date">
                                Fecha de entrega
                            </label>

                            <input
                                id="delivery_date"
                                class="form-control"
                                type="date"
                                v-model="transactionForm.delivery_date"
                                :required="!isExpense"
                                :min="transactionForm.transaction_date"
                            />
                        </div>


                        <!-- Monto manual para egreso -->
                        <div
                            v-if="isExpense"
                            class="form-group col-md-6 col-lg-3 p-2"
                        >
                            <label for="amount">
                                Monto del egreso
                            </label>

                            <input
                                id="amount"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                v-model.number="transactionForm.amount"
                                required
                            />
                        </div>


                        <!-- Anotaciones -->
                        <div class="form-group col-md-12 col-lg-6 p-2">
                            <label for="annotations">
                                Anotaciones
                            </label>

                            <textarea
                                id="annotations"
                                class="form-control"
                                v-model="transactionForm.annotations"
                                rows="2"
                                placeholder="Ingrese una descripción o anotación"
                            ></textarea>
                        </div>

                    </div>


                    <!-- ===================================================== -->
                    <!-- SECCIÓN 2: CLIENTE -->
                    <!-- Solo para ingresos -->
                    <!-- ===================================================== -->

                    <div
                        v-if="!isExpense"
                        class="mt-3"
                    >
                        <h3 class="card-subtitle">
                            Datos del cliente
                        </h3>

                        <div class="row">

                            <div class="form-group col-lg-4">
                                <label for="company_ruc">
                                    RUC
                                </label>

                                <input
                                    id="company_ruc"
                                    class="form-control"
                                    type="text"
                                    v-model="clientForm.company_ruc"
                                    placeholder="Ingrese el RUC del cliente"
                                    :disabled="clientSelected"
                                    @input="onRucInput"
                                    autocomplete="off"
                                />

                                <ul
                                    v-if="clientMatches.length && !clientSelected"
                                    class="list-group position-absolute"
                                    style="z-index: 10;"
                                >
                                    <li
                                        v-for="match in clientMatches"
                                        :key="match.id"
                                        class="list-group-item list-group-item-action"
                                        style="cursor: pointer;"
                                        @click="selectClientMatch(match)"
                                    >
                                        {{ match.company_ruc }} —
                                        {{ match.name }}

                                        <span
                                            v-if="match.company_name"
                                            class="text-muted"
                                        >
                                            ({{ match.company_name }})
                                        </span>
                                    </li>
                                </ul>

                                <small
                                    v-if="
                                        rucSearched &&
                                        !clientMatches.length &&
                                        !clientSelected
                                    "
                                    class="text-muted"
                                >
                                    Sin coincidencias, complete los datos para
                                    registrar un cliente nuevo.
                                </small>
                            </div>


                            <div class="form-group col-lg-4">
                                <label for="client_company_name">
                                    Razón social
                                </label>

                                <input
                                    id="client_company_name"
                                    class="form-control"
                                    type="text"
                                    v-model="clientForm.company_name"
                                    :disabled="clientSelected"
                                />
                            </div>


                            <div class="form-group col-lg-4">
                                <label for="client_name">
                                    Nombre del responsable
                                </label>

                                <input
                                    id="client_name"
                                    class="form-control"
                                    type="text"
                                    v-model="clientForm.name"
                                    :disabled="
                                        clientSelected &&
                                        !addingRepresentative
                                    "
                                />
                            </div>

                        </div>


                        <div class="row">

                            <div class="form-group col-lg-4">
                                <label for="client_phone">
                                    Teléfono
                                </label>

                                <input
                                    id="client_phone"
                                    class="form-control"
                                    type="text"
                                    v-model="clientForm.phone"
                                    :disabled="
                                        clientSelected &&
                                        !addingRepresentative
                                    "
                                />
                            </div>


                            <div class="form-group col-lg-4">
                                <label for="client_email">
                                    Email
                                </label>

                                <input
                                    id="client_email"
                                    class="form-control"
                                    type="email"
                                    v-model="clientForm.email"
                                    :disabled="
                                        clientSelected &&
                                        !addingRepresentative
                                    "
                                />
                            </div>


                            <div class="form-group col-lg-4">
                                <label for="client_address">
                                    Dirección
                                </label>

                                <input
                                    id="client_address"
                                    class="form-control"
                                    type="text"
                                    v-model="clientForm.address"
                                    :disabled="
                                        clientSelected &&
                                        !addingRepresentative
                                    "
                                />
                            </div>

                        </div>


                        <div
                            v-if="clientSelected"
                            class="d-flex gap-2 mt-2"
                        >
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm"
                                @click="addRepresentative"
                            >
                                <i class="bi bi-person-plus me-1"></i>
                                Agregar representante
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-sm"
                                @click="resetClientSelection"
                            >
                                <i class="bi bi-search me-1"></i>
                                Buscar otro RUC
                            </button>
                        </div>

                    </div>


                    <!-- ===================================================== -->
                    <!-- SECCIÓN 3: PROMOCIÓN -->
                    <!-- Solo para ingresos -->
                    <!-- ===================================================== -->

                    <div
                        v-if="!isExpense"
                        class="modulePromotions p-2 mt-3"
                    >
                        <h3 class="card-subtitle">
                            Promociones disponibles
                        </h3>

                        <Multiselect
                            v-model="selectedPromotion"
                            :options="promotions"
                            :searchable="true"
                            :close-on-select="true"
                            :allow-empty="true"
                            placeholder="Buscar promoción..."
                            label="name"
                            track-by="id"
                            select-label=""
                            selected-label=""
                            deselect-label=""
                            no-options="No hay promociones disponibles"
                            no-result="No se encontraron promociones"
                        >

                            <template #option="{ option }">
                                <div
                                    class="d-flex justify-content-between align-items-center w-100"
                                >
                                    <span>
                                        {{ option.name }}
                                    </span>

                                    <span
                                        class="fw-bold text-danger ms-3"
                                    >
                                        S/ -{{
                                            Number(
                                                option.discount_value
                                            ).toFixed(2)
                                        }}
                                    </span>
                                </div>
                            </template>


                            <template #singleLabel="{ option }">
                                <span>
                                    {{ option.name }}

                                    <strong
                                        class="ms-2 text-danger"
                                    >
                                        S/ -{{
                                            Number(
                                                option.discount_value
                                            ).toFixed(2)
                                        }}
                                    </strong>
                                </span>
                            </template>

                        </Multiselect>
                    </div>


                    <!-- ===================================================== -->
                    <!-- SECCIÓN 4: SERVICIOS -->
                    <!-- Solo para ingresos -->
                    <!-- ===================================================== -->

                    <div
                        v-if="!isExpense"
                        class="moduleServices p-2 mt-3"
                    >

                        <div
                            class="d-flex flex-wrap align-items-center justify-content-between"
                        >
                            <h3 class="card-subtitle">
                                Servicios
                            </h3>

                            <button
                                type="button"
                                class="btn btn-primary"
                                @click="openServiceModal"
                            >
                                Agregar servicio
                            </button>
                        </div>


                        <div>

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered mt-2"
                                    v-if="selectedServices.length"
                                >

                                    <thead>
                                        <tr>
                                            <th>Servicio</th>
                                            <th style="width: 140px;">
                                                Precio unitario
                                            </th>
                                            <th style="width: 140px;">
                                                Cantidad
                                            </th>
                                            <th style="width: 140px;">
                                                Subtotal
                                            </th>
                                            <th style="width: 80px;"></th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <tr
                                            v-for="item in selectedServices"
                                            :key="item.service_id"
                                        >

                                            <td>
                                                {{ item.name }}
                                            </td>

                                            <td>
                                                {{ formatCurrency(item.price) }}
                                            </td>

                                            <td>
                                                <input
                                                    type="number"
                                                    min="1"
                                                    class="form-control form-control-sm"
                                                    v-model.number="item.quantity"
                                                    @input="
                                                        normalizeQuantity(item)
                                                    "
                                                />
                                            </td>

                                            <td>
                                                {{
                                                    formatCurrency(
                                                        subtotalFor(item)
                                                    )
                                                }}
                                            </td>

                                            <td class="text-center">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    @click="
                                                        removeService(
                                                            item.service_id
                                                        )
                                                    "
                                                >
                                                    &times;
                                                </button>
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>


                                <p
                                    v-else
                                    class="fst-italic"
                                >
                                    Aún no se han agregado servicios.
                                </p>

                            </div>

                        </div>


                        <div class="d-flex justify-content-end">
                            <h6>
                                SubTotal:
                                {{ formatCurrency(subtotal) }}
                            </h6>
                        </div>


                        <div
                            class="d-flex justify-content-end"
                            v-if="selectedPromotion"
                        >
                            <h6>
                                Descuento:
                                {{ formatCurrency(discount) }}
                            </h6>
                        </div>


                        <div class="d-flex justify-content-end">
                            <h3>
                                Total:
                                {{ formatCurrency(total) }}
                            </h3>
                        </div>

                    </div>


                    <!-- ===================================================== -->
                    <!-- ERROR -->
                    <!-- ===================================================== -->

                    <p
                        v-if="error"
                        class="text-danger mt-3"
                    >
                        {{ error }}
                    </p>


                    <!-- ===================================================== -->
                    <!-- BOTONES -->
                    <!-- ===================================================== -->

                    <div
                        class="d-flex gap-2 mt-3 justify-content-center"
                    >

                        <button
                            type="button"
                            class="btn btn-danger"
                            @click="goBack"
                        >
                            Volver
                        </button>


                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="registerSale"
                        >
                            Registrar
                        </button>


                        <button
                            v-if="
                                auth.hasPermission('payments.create') &&
                                !isExpense
                            "
                            type="button"
                            class="btn btn-success"
                            @click="giveAdvance"
                        >
                            Dar adelanto
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- ============================================================= -->
    <!-- MODAL: SELECCIONAR SERVICIOS -->
    <!-- ============================================================= -->

    <div v-if="showServiceModal">

        <div
            class="modal d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,0.5);"
        >

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h2 class="modal-title">
                            Servicios disponibles
                        </h2>

                        <button
                            type="button"
                            class="btn-close"
                            @click="closeServiceModal"
                        ></button>

                    </div>


                    <div class="modal-body">

                        <div class="table-responsive">

                            <DataTable
                                :data="activeServices"
                                :columns="serviceModalColumns"
                            >

                                <template #column-0="props">
                                    <div class="text-center">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            :checked="
                                                tempSelectedServiceIds.includes(
                                                    props.rowData.id
                                                )
                                            "
                                            @change="
                                                toggleTempService(
                                                    props.rowData.id
                                                )
                                            "
                                        />

                                    </div>
                                </template>


                                <template #column-1="props">
                                    <span>
                                        {{ props.rowData.name }}
                                    </span>
                                </template>


                                <template #column-2="props">
                                    <span>
                                        {{
                                            formatCurrency(
                                                props.rowData.price
                                            )
                                        }}
                                    </span>
                                </template>

                            </DataTable>

                        </div>

                    </div>


                    <div class="modal-footer justify-content-center">

                        <button
                            type="button"
                            class="btn btn-success"
                            @click="confirmAddServices"
                        >
                            Agregar
                        </button>

                        <button
                            type="button"
                            class="btn btn-danger"
                            @click="closeServiceModal"
                        >
                            Cancelar
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- MODAL: REGISTRAR PAGO / ADELANTO -->
    <!-- ============================================================= -->

    <div v-if="showPaymentModal">

        <div
            class="modal d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,0.5);"
        >

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Registrar pago
                        </h5>
                    </div>


                    <div class="modal-body">

                        <div class="form-group">
                            <label for="payment_method_id">
                                Método de pago
                            </label>

                            <select
                                id="payment_method_id"
                                class="form-select"
                                v-model="paymentForm.payment_method_id"
                                required
                            >
                                <option
                                    value=""
                                    disabled
                                >
                                    Seleccione un método
                                </option>

                                <option
                                    v-for="method in paymentMethods"
                                    :key="method.id"
                                    :value="method.id"
                                >
                                    {{ method.name }}
                                </option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="payment_amount">
                                Monto
                            </label>

                            <input
                                id="payment_amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="form-control"
                                v-model.number="paymentForm.amount"
                                required
                            />
                        </div>


                        <div class="form-group">
                            <label for="payment_type">
                                Tipo de pago
                            </label>

                            <select
                                id="payment_type"
                                class="form-select"
                                v-model="paymentForm.payment_type"
                                required
                            >
                                <option value="advance">
                                    Adelanto
                                </option>

                                <option value="balance">
                                    Saldo
                                </option>

                                <option value="full">
                                    Pago completo
                                </option>
                            </select>
                        </div>


                        <p
                            v-if="paymentError"
                            class="text-danger"
                        >
                            {{ paymentError }}
                        </p>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="cancelPayment"
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="savePayment"
                        >
                            Guardar
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>


<script setup>

import Preloader from '@/components/layout/preloader.vue'
import DataTable from '@/components/tables/dataTable.vue'

import ClientService from '@/services/ClientService'
import TransactionService from '@/services/transactionService'
import ServicesService from '@/services/ServicesService'
import PromotionsService from '@/services/PromotionService'
import PaymentMethodService from '@/services/PaymentMethodService'

import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

import { showSuccess } from '@/utils/sweetAlert'
import { useAuthStore } from '@/stores/auth'


// =============================================================
// CONFIGURACIÓN
// =============================================================

const router = useRouter()
const cargando = ref(false)
const error = ref('')

const auth = useAuthStore()


// =============================================================
// USUARIO / ROLES
// =============================================================

// El role viene directamente en el usuario
const isAdmin = computed(() => auth.user?.role === 'admin')

const isEmployee = computed(() => auth.user?.role === 'employee')


// Tipo de transacción
const isExpense = computed(
    () => transactionForm.value.transaction_type === 'expense'
)


// =============================================================
// FECHA
// =============================================================

const todayIso = () => new Date().toISOString().slice(0, 10)


// =============================================================
// SECCIÓN 1: DATOS DE LA TRANSACCIÓN
// =============================================================

const transactionForm = ref({

    transaction_type: '',

    transaction_date: todayIso(),

    delivery_date: '',

    responsible: '',

    annotations: '',

    amount: null,

})


// =============================================================
// CONFIGURACIÓN INICIAL SEGÚN EL ROL
// =============================================================

if (isEmployee.value) {

    transactionForm.value.transaction_type = 'income'

    transactionForm.value.responsible = 'edgar'

}


// =============================================================
// CUANDO CAMBIA EL TIPO DE TRANSACCIÓN
// =============================================================

watch(
    () => transactionForm.value.transaction_type,

    (type) => {

        if (type === 'expense') {

            // Limpiar cliente
            selectedClientId.value = null

            clientMatches.value = []

            rucSearched.value = false

            addingRepresentative.value = false

            clientForm.value = {
                name: '',
                phone: '',
                email: '',
                address: '',
                company_name: '',
                company_ruc: '',
            }


            // Limpiar promoción
            selectedPromotion.value = null
            selectedPromotionId.value = null


            // Limpiar servicios
            selectedServices.value = []


            // Limpiar fecha de entrega
            transactionForm.value.delivery_date = ''

        }

    }
)


// =============================================================
// SECCIÓN 2: CLIENTE
// =============================================================

const clientForm = ref({

    name: '',
    phone: '',
    email: '',
    address: '',
    company_name: '',
    company_ruc: '',

})

const clientMatches = ref([])

const selectedClientId = ref(null)

const clientSelected = computed(
    () => selectedClientId.value !== null
)

const addingRepresentative = ref(false)

const rucSearched = ref(false)

let rucSearchTimeout = null


const onRucInput = () => {

    clearTimeout(rucSearchTimeout)

    rucSearched.value = false

    clientMatches.value = []

    const ruc = clientForm.value.company_ruc.trim()

    if (ruc.length < 3) {
        return
    }

    rucSearchTimeout = setTimeout(async () => {

        try {

            const response =
                await ClientService.searchByRuc(ruc)

            clientMatches.value =
                response.data.data ?? response.data

        } catch (err) {

            console.error(
                'Error al buscar cliente por RUC:',
                err
            )

        } finally {

            rucSearched.value = true

        }

    }, 400)
}


const selectClientMatch = (match) => {

    selectedClientId.value = match.id

    clientForm.value = {

        name: match.name ?? '',

        phone: match.phone ?? '',

        email: match.email ?? '',

        address: match.address ?? '',

        company_name: match.company_name ?? '',

        company_ruc: match.company_ruc ?? '',

    }

    clientMatches.value = []

}


const addRepresentative = () => {

    clientForm.value.name = ''
    clientForm.value.phone = ''
    clientForm.value.email = ''
    clientForm.value.address = ''

    addingRepresentative.value = true

}


const resetClientSelection = () => {

    addingRepresentative.value = false

    selectedClientId.value = null

    clientMatches.value = []

    rucSearched.value = false

    clientForm.value = {

        name: '',
        phone: '',
        email: '',
        address: '',
        company_name: '',
        company_ruc: '',

    }

}


// Devuelve el client_id a utilizar
const ensureClientId = async () => {

    // Si no hay datos de cliente,
    // simplemente no se crea un cliente.
    if (
        !clientSelected.value &&
        !clientForm.value.name &&
        !clientForm.value.company_ruc
    ) {
        return null
    }


    if (selectedClientId.value) {
        return selectedClientId.value
    }


    const response =
        await ClientService.createClient({

            name: clientForm.value.name,

            phone: clientForm.value.phone,

            email: clientForm.value.email,

            address: clientForm.value.address,

            company_name: clientForm.value.company_name,

            company_ruc: clientForm.value.company_ruc,

        })


    return response.data.data?.id ?? response.data.id

}


// =============================================================
// SECCIÓN 3: PROMOCIONES
// =============================================================

const promotions = ref([])

const selectedPromotion = ref(null)

const selectedPromotionId = ref(null)


const getActivePromotions = async () => {

    try {

        const response =
            await PromotionsService.getPromotions()

        const all =
            response.data.data ?? response.data

        promotions.value =
            all.filter(
                (p) =>
                    p.status === 'active' &&
                    p.discount_type === 'fixed'
            )

    } catch (err) {

        console.error(
            'Error al cargar promociones:',
            err
        )

        error.value =
            'No se pudieron cargar las promociones'

    }

}


// =============================================================
// SECCIÓN 4: SERVICIOS
// =============================================================

const allServices = ref([])

const selectedServices = ref([])

const showServiceModal = ref(false)

const tempSelectedServiceIds = ref([])


const activeServices = computed(() =>
    allServices.value.filter(
        (s) => s.status === 'active'
    )
)


const serviceModalColumns = [

    {
        data: 'id',
        title: 'Selección',
        className: 'text-center'
    },

    {
        data: 'name',
        title: 'Nombre',
        className: 'text-center'
    },

    {
        data: 'price',
        title: 'Precio',
        className: 'text-center'
    },

]


const getServices = async () => {

    try {

        const response =
            await ServicesService.getServices()

        allServices.value =
            response.data.data ?? response.data

    } catch (err) {

        console.error(
            'Error al cargar servicios:',
            err
        )

        error.value =
            'No se pudieron cargar los servicios'

    }

}


const openServiceModal = () => {

    tempSelectedServiceIds.value =
        selectedServices.value.map(
            (s) => s.service_id
        )

    showServiceModal.value = true

}


const closeServiceModal = () => {

    showServiceModal.value = false

}


const toggleTempService = (serviceId) => {

    const index =
        tempSelectedServiceIds.value.indexOf(
            serviceId
        )

    if (index === -1) {

        tempSelectedServiceIds.value.push(
            serviceId
        )

    } else {

        tempSelectedServiceIds.value.splice(
            index,
            1
        )

    }

}


const confirmAddServices = () => {

    selectedServices.value =
        tempSelectedServiceIds.value.map(
            (serviceId) => {

                const existing =
                    selectedServices.value.find(
                        (s) =>
                            s.service_id === serviceId
                    )

                if (existing) {
                    return existing
                }


                const service =
                    allServices.value.find(
                        (s) => s.id === serviceId
                    )


                return {

                    service_id: serviceId,

                    name: service.name,

                    price: Number(service.price),

                    quantity: 1,

                }

            }
        )


    showServiceModal.value = false

}


const removeService = (serviceId) => {

    selectedServices.value =
        selectedServices.value.filter(
            (s) =>
                s.service_id !== serviceId
        )

}


const normalizeQuantity = (item) => {

    if (!item.quantity || item.quantity < 1) {
        item.quantity = 1
    }

}


const subtotalFor = (item) =>
    item.price * item.quantity


const subtotal = computed(() =>

    selectedServices.value.reduce(
        (sum, item) =>
            sum + subtotalFor(item),
        0
    )

)


const discount = computed(() => {

    if (!selectedPromotion.value) {
        return 0
    }


    return Math.min(

        Number(
            selectedPromotion.value.discount_value
        ),

        subtotal.value

    )

})


const total = computed(() =>

    subtotal.value - discount.value

)


const formatCurrency = (value) =>

    new Intl.NumberFormat(
        'es-PE',
        {
            style: 'currency',
            currency: 'PEN'
        }
    ).format(value || 0)


// =============================================================
// REGISTRO DE TRANSACCIÓN
// =============================================================

const buildDetailsPayload = () =>

    selectedServices.value.map(
        (item) => ({

            service_id: item.service_id,

            unit_price: item.price,

            quantity: item.quantity,

        })
    )


const validateBeforeSubmit = () => {

    error.value = ''


    // =========================================================
    // EGRESO
    // =========================================================

    if (isExpense.value) {

        if (
            transactionForm.value.amount === null ||
            transactionForm.value.amount === '' ||
            Number(transactionForm.value.amount) <= 0
        ) {

            error.value =
                'Ingrese un monto válido para el egreso.'

            return false

        }


        if (
            !transactionForm.value.annotations?.trim()
        ) {

            error.value =
                'Ingrese una anotación para el egreso.'

            return false

        }


        return true

    }


    // =========================================================
    // INGRESO
    // =========================================================

    // Cliente es opcional
    // Servicios son opcionales
    // Promoción es opcional

    if (total.value <= 0) {

        error.value =
            'El monto de la transacción debe ser mayor a cero.'

        return false

    }


    return true

}


// =============================================================
// CREAR TRANSACCIÓN
// =============================================================

const createTransaction = async () => {

    let clientId = null


    // Solo buscamos/creamos cliente para ingresos
    if (!isExpense.value) {

        clientId =
            await ensureClientId()

    }


    const payload = {

        client_id:
            isExpense.value
                ? null
                : clientId,

        promotion_id:
            isExpense.value
                ? null
                : selectedPromotion.value?.id ?? null,

        transaction_date:
            transactionForm.value.transaction_date,

        transaction_type:
            transactionForm.value.transaction_type,

        delivery_date:
            isExpense.value
                ? null
                : transactionForm.value.delivery_date || null,

        responsible:
            transactionForm.value.responsible,

        annotations:
            transactionForm.value.annotations?.trim() || null,

        amount:
            isExpense.value
                ? Number(
                    transactionForm.value.amount
                )
                : Number(total.value),

        details:
            isExpense.value
                ? []
                : buildDetailsPayload(),

    }


    const response =
        await TransactionService.createTransaction(
            payload
        )


    return response.data.data ?? response.data

}


// =============================================================
// REGISTRAR
// =============================================================

const registerSale = async () => {

    if (!validateBeforeSubmit()) {
        return
    }


    try {
        await createTransaction()

        await showSuccess(
            'Se registró la transacción correctamente'
        )

        goBack()

    } catch (err) {

        if (err.response?.data?.errors) {

            error.value =
                Object.values(
                    err.response.data.errors
                )
                    .flat()
                    .join(' ')

        } else {

            error.value =
                'No se pudo registrar la transacción'

        }

    } 

}


// =============================================================
// MODAL DE PAGO / ADELANTO
// =============================================================

const showPaymentModal = ref(false)

const createdTransactionId = ref(null)

const paymentMethods = ref([])


const paymentForm = ref({

    payment_method_id: '',

    amount: '',

    payment_type: 'advance',

})


const paymentError = ref('')


const getPaymentMethods = async () => {

    try {

        const response =
            await PaymentMethodService.getPaymentMethods()

        paymentMethods.value =
            response.data.data ?? response.data

    } catch (err) {

        console.error(
            'Error al cargar métodos de pago:',
            err
        )

    }

}


const giveAdvance = async () => {

    if (isExpense.value) {
        return
    }


    if (!validateBeforeSubmit()) {
        return
    }


    try {

        const transaction =
            await createTransaction()

        createdTransactionId.value =
            transaction.id

        showPaymentModal.value = true

    } catch (err) {

        if (err.response?.data?.errors) {

            error.value =
                Object.values(
                    err.response.data.errors
                )
                    .flat()
                    .join(' ')

        } else {

            error.value =
                'No se pudo registrar la transacción'

        }

    } 
}


const savePayment = async () => {

    paymentError.value = ''


    if (
        !paymentForm.value.payment_method_id ||
        !paymentForm.value.amount
    ) {

        paymentError.value =
            'Complete el método de pago y el monto.'

        return

    }


    try {

        await TransactionService.addPayment({

            transaction_id:
                createdTransactionId.value,

            payment_method_id:
                paymentForm.value.payment_method_id,

            amount:
                paymentForm.value.amount,

            payment_type:
                paymentForm.value.payment_type,

        })


        await showSuccess(
            'Se registró la transacción y el adelanto correctamente'
        )

        goToSalesboard()

    } catch (err) {

        if (err.response?.data?.errors) {

            paymentError.value =
                Object.values(
                    err.response.data.errors
                )
                    .flat()
                    .join(' ')

        } else {

            paymentError.value =
                'No se pudo registrar el pago'

        }

    }

}


const cancelPayment = () => {

    goToSalesboard()

}


// =============================================================
// NAVEGACIÓN
// =============================================================

const goBack = () => {

    goToSalesboard()

}


const goToSalesboard = () => {

    router.push({
        name: 'salesboard'
    })

}


// =============================================================
// CARGA INICIAL
// =============================================================

onMounted(async () => {

    cargando.value = true

    try {

        await Promise.all([

            getActivePromotions(),

            getServices(),

            getPaymentMethods(),

        ])

    } finally {

        cargando.value = false

    }

})

</script>