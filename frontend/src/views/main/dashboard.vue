<template>
    <div class="container-fluid salesboard">
        <Preloader :visible="cargando"></Preloader>

        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <h1 class="card-title fw-bold">Dashboard</h1>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <select class="form-select" v-model="period" @change="onPeriodChange">
                    <option value="current_month">Mes actual</option>
                    <option value="previous_month">Mes anterior</option>
                    <option value="last_6_months">Últimos 6 meses</option>
                    <option value="current_year">Este año</option>
                    <option value="custom">Personalizado</option>
                </select>

                <template v-if="period === 'custom'">
                    <input type="date" class="form-control" v-model="dateFrom" />
                    <input type="date" class="form-control" v-model="dateTo" />
                    <button type="button" class="btn btn-primary" @click="fetchReport">Filtrar</button>
                </template>

                <div class="vr d-none d-md-block"></div>

                <button type="button" class="btn btn-outline-secondary" :disabled="exporting" @click="exportGeneralReport">
                    <i class="bi bi-download"></i> Exportar general
                </button>

                <div class="position-relative">
                    <button type="button" class="btn btn-outline-secondary" :disabled="exporting"
                        @click="showExportPeriodPanel = !showExportPeriodPanel">
                        <i class="bi bi-calendar-range"></i> Exportar por periodo
                    </button>

                    <div v-if="showExportPeriodPanel" class="export-period-panel card shadow-sm p-3">
                        <label class="form-label small mb-1">Desde</label>
                        <input type="date" class="form-control form-control-sm mb-2" v-model="exportPeriodFrom" />
                        <label class="form-label small mb-1">Hasta</label>
                        <input type="date" class="form-control form-control-sm mb-3" v-model="exportPeriodTo" />
                        <button type="button" class="btn btn-primary btn-sm w-100" :disabled="exporting"
                            @click="exportPeriodReport">
                            Exportar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <p v-if="error" style="color: red;">{{ error }}</p>

        <!-- KPI cards -->
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card border-start border-4 border-success h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Ingresos</p>
                            <h3 class="mb-0">{{ formatCurrency(kpis.income) }}</h3>
                            <GrowthBadge :value="kpis.income_growth_percentage" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card border-start border-4 border-danger h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-graph-down-arrow"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Egresos</p>
                            <h3 class="mb-0">{{ formatCurrency(kpis.expenses) }}</h3>
                            <small class="text-muted">Promedio: {{ formatCurrency(kpis.average_expense) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card border-start border-4 border-primary h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Utilidad neta</p>
                            <h3 class="mb-0">{{ formatCurrency(kpis.net_profit) }}</h3>
                            <GrowthBadge :value="kpis.profit_growth_percentage" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-card border-start border-4 border-warning h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Periodo</p>
                            <div class="d-flex flex-wrap gap-1">
                                <h6 class="mb-0">{{ period_.from }}</h6>
                                <h6>-</h6>
                                <h6 class="mb-0">{{ period_.to }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avance del periodo + Dividendos + Medio de pago -->
        <div class="row g-3 mt-1">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Avance del periodo</h5>
                        <div class="chart-wrapper">
                            <Line v-if="advanceChartData.labels.length" :data="advanceChartData" :options="advanceChartOptions" />
                            <p v-else class="text-muted">No hay datos para el periodo seleccionado.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">Dividendos del mes</h5>
                        <div class="chart-wrapper chart-wrapper-sm">
                            <Bar :data="dividendsChartData" :options="dividendsChartOptions" />
                        </div>
                    </div>
                </div>

                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">Acumulado por medio de pago</h5>
                        <div class="chart-wrapper chart-wrapper-sm">
                            <Doughnut v-if="paymentMethodChartData.labels.length" :data="paymentMethodChartData"
                                :options="paymentMethodChartOptions" />
                            <p v-else class="text-muted">Sin datos de medios de pago en este periodo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transacciones + ranking por usuario -->
        <div class="row g-3 mt-1">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Transacciones del periodo</h5>
                        <div class="table-responsive">
                            <DataTable :data="transactions" :columns="transactionsColumns">
                                <template #column-0="props">
                                    {{ props.rowData.transaction_date }}
                                </template>
                                <template #column-1="props">
                                    {{ props.rowData.client?.name ?? '—' }}
                                </template>
                                <template #column-2="props">
                                    <span class="badge" :class="props.rowData.transaction_type === 'income' ? 'bg-success' : 'bg-danger'">
                                        {{ props.rowData.transaction_type === 'income' ? 'Ingreso' : 'Egreso' }}
                                    </span>
                                </template>
                                <template #column-3="props">
                                    {{ formatCurrency(props.rowData.amount) }}
                                </template>
                                <template #column-4="props">
                                    <span class="badge" :class="statusBadgeClass(props.rowData.status)">
                                        {{ statusLabel(props.rowData.status) }}
                                    </span>
                                </template>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Ventas por usuario</h5>

                        <div v-if="byUser.length">
                            <div v-for="user in byUser" :key="user.user_id" class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ user.user_name }}</span>
                                    <span class="fw-bold">{{ formatCurrency(user.income) }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success"
                                        :style="{ width: userBarWidth(user) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-muted">Sin datos por usuario en este periodo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import DataTable from '@/components/tables/dataTable.vue'
import TransactionService from '@/services/transactionService'
import { computed, defineComponent, h, onMounted, ref } from 'vue'
import { Line, Bar, Doughnut } from 'vue-chartjs'
import 'chart.js/auto'

const cargando = ref(false)
const error = ref('')

// ---------------------------------------------------------------------------
// Filtro de periodo
// ---------------------------------------------------------------------------
const period = ref('current_month')
const dateFrom = ref('')
const dateTo = ref('')

const toIsoDate = (date) => date.toISOString().slice(0, 10)

const computeDateRange = (selectedPeriod) => {
    const now = new Date()

    if (selectedPeriod === 'current_month') {
        const from = new Date(now.getFullYear(), now.getMonth(), 1)
        const to = new Date(now.getFullYear(), now.getMonth() + 1, 0)
        return { from: toIsoDate(from), to: toIsoDate(to) }
    }

    if (selectedPeriod === 'previous_month') {
        const from = new Date(now.getFullYear(), now.getMonth() - 1, 1)
        const to = new Date(now.getFullYear(), now.getMonth(), 0)
        return { from: toIsoDate(from), to: toIsoDate(to) }
    }

    if (selectedPeriod === 'last_6_months') {
        const from = new Date(now.getFullYear(), now.getMonth() - 5, 1)
        const to = new Date(now.getFullYear(), now.getMonth() + 1, 0)
        return { from: toIsoDate(from), to: toIsoDate(to) }
    }

    if (selectedPeriod === 'current_year') {
        const from = new Date(now.getFullYear(), 0, 1)
        const to = new Date(now.getFullYear(), 11, 31)
        return { from: toIsoDate(from), to: toIsoDate(to) }
    }

    // custom: se respetan los valores que el usuario ya haya elegido
    return { from: dateFrom.value, to: dateTo.value }
}

const onPeriodChange = () => {
    if (period.value === 'custom') {
        return
    }

    const range = computeDateRange(period.value)
    dateFrom.value = range.from
    dateTo.value = range.to
    fetchReport()
}

// ---------------------------------------------------------------------------
// Datos del reporte
// ---------------------------------------------------------------------------
const period_ = ref({ from: '', to: '' })
const kpis = ref({
    income: 0,
    expenses: 0,
    net_profit: 0,
    average_expense: 0,
    income_growth_percentage: 0,
    profit_growth_percentage: 0,
})
// NOTA / SUPUESTO: se espera que el backend agregue este arreglo al reporte,
// con un registro por cada día que ya tiene datos dentro del periodo:
// [{ date: 'YYYY-MM-DD', income: 0, net_profit: 0 }, ...]
const dailySeries = ref([])
// NOTA / SUPUESTO: acumulado por medio de pago, agregado en backend:
// [{ payment_method: 'efectivo', amount: 0 }, ...]
// Mientras no exista este campo, se calcula un fallback en el cliente
// a partir de las transacciones cargadas (ver paymentMethodChartData).
const byPaymentMethod = ref([])
const byUser = ref([])
const transactions = ref([])

const transactionsColumns = [
    { data: 'transaction_date', title: 'Fecha', className: 'text-center' },
    { data: 'client', title: 'Cliente', className: 'text-center' },
    { data: 'transaction_type', title: 'Tipo', className: 'd-none d-sm-table-cell text-center' },
    { data: 'amount', title: 'Monto', className: 'text-center' },
    { data: 'status', title: 'Estado', className: 'text-center' },
]

const fetchReport = async () => {
    cargando.value = true
    error.value = ''

    try {
        const response = await TransactionService.getReports({
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        })

        const report = response.data

        period_.value = report.period
        kpis.value = report.kpis
        dailySeries.value = report.daily_series ?? []
        byPaymentMethod.value = report.by_payment_method ?? []
        byUser.value = report.by_user ?? []

        // El recurso paginado puede venir anidado según cómo se serialice;
        // se contemplan ambas formas para no romper si cambia el wrapping.
        const transactionsReport = report.data?.data?.data ?? report.data?.data ?? report.data ?? []
        transactions.value=transactionsReport.filter(transaction=>transaction.transaction_type==='income');
    } catch (err) {
        console.error('Error al cargar el reporte:', err)
        error.value = 'No se pudo cargar el dashboard'
    } finally {
        cargando.value = false
    }
}

const userMaxIncome = computed(() =>
    byUser.value.reduce((max, user) => Math.max(max, user.income), 0)
)

const userBarWidth = (user) => {
    if (!userMaxIncome.value) {
        return 0
    }

    return Math.round((user.income / userMaxIncome.value) * 100)
}

// ---------------------------------------------------------------------------
// Avance del periodo (ingresos + utilidad neta, día a día)
// ---------------------------------------------------------------------------
const parseIsoDate = (isoString) => {
    const [year, month, day] = isoString.split('-').map(Number)
    return new Date(year, month - 1, day)
}

const enumerateDays = (fromIso, toIso) => {
    if (!fromIso || !toIso) {
        return []
    }

    const days = []
    const current = parseIsoDate(fromIso)
    const end = parseIsoDate(toIso)

    while (current <= end) {
        days.push(new Date(current))
        current.setDate(current.getDate() + 1)
    }

    return days
}

const periodDays = computed(() => enumerateDays(dateFrom.value, dateTo.value))

const spansMultipleMonths = computed(() => {
    const days = periodDays.value
    if (!days.length) return false
    return days[0].getMonth() !== days[days.length - 1].getMonth() ||
        days[0].getFullYear() !== days[days.length - 1].getFullYear()
})

const formatDayLabel = (date) =>
    spansMultipleMonths.value
        ? date.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit' })
        : String(date.getDate())

// Fallback: mientras el backend no exponga report.daily_series, se calcula
// el ingreso/egreso por día a partir de las transacciones ya cargadas
// (agrupando por transaction_date).
const dailySeriesFallback = computed(() => {
    const totals = {}

    transactions.value.forEach((t) => {
        const day = t.transaction_date
        if (!day) return

        if (!totals[day]) {
            totals[day] = { income: 0, incomeProfit: 0, expenses: 0 }
        }

        const amount = Number(t.amount || 0)
        if (t.transaction_type === 'income') {
            totals[day].income += amount
            // Si el recurso expone `profit`, se usa la ganancia real;
            // si no, se degrada a amount (mismo comportamiento anterior).
            totals[day].incomeProfit += Number(t.profit ?? amount)
        } else {
            totals[day].expenses += amount
        }
    })

    return Object.entries(totals).map(([date, v]) => ({
        date,
        income: v.income,
        net_profit: v.incomeProfit - v.expenses,
    }))
})

const dailySeriesMap = computed(() => {
    const source = dailySeries.value.length ? dailySeries.value : dailySeriesFallback.value
    const map = {}
    source.forEach((row) => {
        map[row.date] = row
    })
    return map
})

// Monto de cada día por separado (no acumulado).
const advanceChartData = computed(() => {
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const days = periodDays.value
    const map = dailySeriesMap.value

    const incomeData = []
    const netProfitData = []

    days.forEach((day) => {
        if (day > today) {
            // Días futuros del periodo: sin valor, para que no se dibuje curva.
            incomeData.push(null)
            netProfitData.push(null)
            return
        }

        const iso = toIsoDate(day)
        const row = map[iso]
        incomeData.push(row ? row.income : 0)
        netProfitData.push(row ? row.net_profit : 0)
    })

    return {
        labels: days.map(formatDayLabel),
        datasets: [
            {
                label: 'Ingresos',
                data: incomeData,
                borderColor: '#198754',
                backgroundColor: '#198754',
                tension: 0.3,
                spanGaps: false,
            },
            {
                label: 'Utilidad neta',
                data: netProfitData,
                borderColor: '#0d6efd',
                backgroundColor: '#0d6efd',
                tension: 0.3,
                spanGaps: false,
            },
        ],
    }
})

const advanceChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' },
    },
}

// ---------------------------------------------------------------------------
// Dividendos del mes (utilidad acumulada / 2, entre Sam y Jorge)
// SUPUESTO: se reparte la utilidad neta del periodo actualmente cargado.
// Si "del mes" debe ser siempre el mes en curso sin importar el periodo
// seleccionado en el dropdown, el backend debería exponer un campo aparte
// (p. ej. report.current_month_net_profit) para calcular esto sin
// depender del filtro.
// ---------------------------------------------------------------------------
const dividends = computed(() => {
    const total = kpis.value.net_profit || 0
    return {
        sam: total / 2,
        jorge: total / 2,
    }
})

const dividendsChartData = computed(() => ({
    labels: ['Sam', 'Jorge'],
    datasets: [
        {
            label: 'Dividendos',
            data: [dividends.value.sam, dividends.value.jorge],
            backgroundColor: ['#0d6efd', '#fd7e14'],
            borderRadius: 6,
        },
    ],
}))

const dividendsChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (ctx) => formatCurrency(ctx.raw),
            },
        },
    },
    scales: {
        y: {
            ticks: {
                callback: (value) => formatCurrency(value),
            },
        },
    },
}

// ---------------------------------------------------------------------------
// Acumulado por medio de pago
// ---------------------------------------------------------------------------
// Nombre "crudo" del medio de pago (string tal cual, o el name/label/id de
// la relación cuando viene como objeto). Es el valor sobre el que se basan
// tanto la clave normalizada (methodKey) como la etiqueta (paymentMethodLabel),
// para que ambas funciones SIEMPRE coincidan en qué método representan.
const rawMethodName = (method) => {
    if (method && typeof method === 'object') {
        return method.name ?? method.label ?? method.description ?? method.id ?? null
    }
    return method ?? null
}

// Alias -> clave canónica. Cubre tanto los valores en inglés que puede
// devolver el backend (cash/transfer) como el nombre en español que trae
// la relación (Efectivo/Transferencia), para que no dependan de si el
// dato llega como string plano u objeto.
const METHOD_KEY_ALIASES = {
    cash: 'cash',
    efectivo: 'cash',
    transfer: 'transfer',
    transferencia: 'transfer',
    yape: 'yape',
}

// Clave normalizada del medio de pago: se usa tanto para agrupar montos
// como para asignarle SIEMPRE el mismo color, sin importar en qué orden
// aparezca dentro del periodo consultado ni si el dato viene como string
// u objeto/relación.
const methodKey = (method) => {
    const raw = rawMethodName(method)
    if (raw === null || raw === undefined || raw === '') {
        return 'unknown'
    }

    const slug = String(raw).trim().toLowerCase()
    return METHOD_KEY_ALIASES[slug] ?? slug
}

const PAYMENT_METHOD_LABELS = {
    cash: 'Efectivo',
    transfer: 'Transferencia',
    yape: 'Yape',
}

const paymentMethodLabel = (method) => {
    const key = methodKey(method)
    if (PAYMENT_METHOD_LABELS[key]) {
        return PAYMENT_METHOD_LABELS[key]
    }

    // Método desconocido: se muestra el nombre original tal como vino
    // (útil si el backend agrega un medio de pago nuevo).
    return rawMethodName(method) || 'Otro'
}

// Color fijo por medio de pago (clave normalizada -> color). Si aparece un
// método nuevo que no está en el mapa, se usa DEFAULT_METHOD_COLOR para que
// nunca "robe" el color de otro método ni cambie según el orden de los datos.
const PAYMENT_METHOD_COLORS = {
    cash: '#198754',
    transfer: '#0d6efd',
    yape: '#6700A6',
}
const DEFAULT_METHOD_COLOR = '#adb5bd'
const PENDING_KEY = 'pending'
const PENDING_COLOR = '#dc3545'

const paymentMethodColor = (key) =>
    key === PENDING_KEY
        ? PENDING_COLOR
        : (PAYMENT_METHOD_COLORS[key] ?? DEFAULT_METHOD_COLOR)

// Monto pendiente de cobro dentro del periodo: para cada transacción de
// tipo ingreso, lo ya pagado (suma de transactionPayments) se resta del
// monto total; el saldo restante se acumula como "Pendiente".
const pendingAmount = computed(() =>
    transactions.value.reduce((sum, t) => {
        if (t.transaction_type !== 'income') {
            return sum
        }

        const paid = Array.isArray(t.transactionPayments)
            ? t.transactionPayments.reduce((s, p) => s + Number(p.amount || 0), 0)
            : 0

        const amount = Number(t.amount || 0)

        return sum + Math.max(amount - paid, 0)
    }, 0)
)

const paymentMethodSummary = computed(() => {
    let rows

    if (byPaymentMethod.value.length) {
        rows = byPaymentMethod.value.map((row) => ({
            key: methodKey(row.payment_method),
            method: row.payment_method,
            amount: row.amount,
        }))
    } else {
        // Fallback en el cliente mientras el backend no exponga by_payment_method:
        // cada transacción de tipo Ingreso puede tener varios pagos en
        // transactionPayments, y cada pago su propio medio de pago.
        // Los egresos NO participan de este acumulado (solo interesa cómo
        // se están cobrando los ingresos), y el saldo aún no pagado de un
        // ingreso no se inventa aquí como si fuera un pago: ese monto ya
        // se refleja aparte en "Pendiente" (ver pendingAmount), así se
        // evita contarlo dos veces.
        const totals = {}

        transactions.value.forEach((t) => {
            if (t.transaction_type !== 'income') {
                return
            }

            if (Array.isArray(t.transactionPayments) && t.transactionPayments.length) {
                t.transactionPayments.forEach((p) => {
                    const key = methodKey(p.payment_method)
                    if (!totals[key]) {
                        totals[key] = { amount: 0, raw: p.payment_method }
                    }
                    totals[key].amount += Number(p.amount || 0)
                })
                return
            }

            // Compatibilidad hacia atrás: si la transacción ya está
            // completamente pagada pero no viene la relación
            // transactionPayments cargada, se asume un único pago con el
            // medio de pago propio de la transacción. Si no está
            // completamente pagada, no se registra nada aquí (su saldo
            // ya lo cubre "Pendiente").
            if (t.status === 'paid') {
                const key = methodKey(t.payment_method)
                if (!totals[key]) {
                    totals[key] = { amount: 0, raw: t.payment_method }
                }
                totals[key].amount += Number(t.amount || 0)
            }
        })

        rows = Object.entries(totals).map(([key, v]) => ({
            key,
            method: v.raw,
            amount: v.amount,
        }))
    }

    // Se agrega el monto pendiente como una porción más del gráfico, con
    // color y etiqueta propios, para que el total refleje lo facturado
    // en el periodo (cobrado por medio de pago + lo que falta cobrar).
    if (pendingAmount.value > 0) {
        rows = [...rows, { key: PENDING_KEY, method: PENDING_KEY, amount: pendingAmount.value }]
    }

    return rows
})

const paymentMethodChartData = computed(() => ({
    labels: paymentMethodSummary.value.map((row) =>
        row.key === PENDING_KEY ? 'Pendiente' : paymentMethodLabel(row.method)
    ),
    datasets: [
        {
            data: paymentMethodSummary.value.map((row) => row.amount),
            backgroundColor: paymentMethodSummary.value.map((row) => paymentMethodColor(row.key)),
        },
    ],
}))

const paymentMethodTotal = computed(() =>
    paymentMethodSummary.value.reduce((sum, row) => sum + row.amount, 0)
)

const paymentMethodChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' },
        tooltip: {
            callbacks: {
                label: (ctx) => `${ctx.label}: ${formatCurrency(ctx.raw)}`,
            },
        },
        datalabels: {
            color: '#fff',
            font: { weight: 'bold', size: 11 },
            formatter: (value) => {
                if (!paymentMethodTotal.value) {
                    return ''
                }
                const percentage = (value / paymentMethodTotal.value) * 100
                // Se ocultan las etiquetas de porciones muy pequeñas para
                // no saturar el gráfico con texto ilegible.
                return percentage < 5 ? '' : `${percentage.toFixed(0)}%`
            },
        },
    },
}

// ---------------------------------------------------------------------------
// Exportación de reportes
// SUPUESTO: existe (o se agregará) TransactionService.exportReport(params, config)
// que golpea un endpoint del backend y devuelve el archivo como blob
// (config: { responseType: 'blob' }). Ajustar nombre/firma según la API real.
// ---------------------------------------------------------------------------
const exporting = ref(false)
const showExportPeriodPanel = ref(false)
const exportPeriodFrom = ref('')
const exportPeriodTo = ref('')

const downloadBlob = (blob, filename) => {
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
}

const exportGeneralReport = async () => {
    exporting.value = true
    error.value = ''

    try {
        const range = computeDateRange('current_month')
        const response = await TransactionService.exportReport(
            { date_from: range.from, date_to: range.to },
            { responseType: 'blob' }
        )
        downloadBlob(response.data, `reporte-general-${range.from}.xlsx`)
    } catch (err) {
        console.error('Error al exportar el reporte general:', err)
        error.value = 'No se pudo exportar el reporte general'
    } finally {
        exporting.value = false
    }
}

const exportPeriodReport = async () => {
    if (!exportPeriodFrom.value || !exportPeriodTo.value) {
        error.value = 'Selecciona fecha de inicio y fin para exportar'
        return
    }

    exporting.value = true
    error.value = ''

    try {
        const response = await TransactionService.exportReport(
            { date_from: exportPeriodFrom.value, date_to: exportPeriodTo.value },
            { responseType: 'blob' }
        )
        downloadBlob(response.data, `reporte-${exportPeriodFrom.value}-a-${exportPeriodTo.value}.xlsx`)
        showExportPeriodPanel.value = false
    } catch (err) {
        console.error('Error al exportar el reporte por periodo:', err)
        error.value = 'No se pudo exportar el reporte del periodo'
    } finally {
        exporting.value = false
    }
}

// ---------------------------------------------------------------------------
// Formato / badges
// ---------------------------------------------------------------------------
const formatCurrency = (value) =>
    new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value || 0)

const statusLabel = (status) => ({
    pending: 'Pendiente',
    partially_paid: 'Pago parcial',
    paid: 'Pagado',
}[status] ?? status)

const statusBadgeClass = (status) => ({
    pending: 'bg-secondary',
    partially_paid: 'bg-warning text-dark',
    paid: 'bg-success',
}[status] ?? 'bg-secondary')

// Pequeño badge de crecimiento (verde si sube, rojo si baja), como componente local.
const GrowthBadge = defineComponent({
    props: { value: { type: Number, default: 0 } },
    setup(props) {
        return () => {
            const positive = props.value >= 0

            return h(
                'small',
                { class: positive ? 'text-success' : 'text-danger' },
                [
                    h('i', { class: positive ? 'bi bi-arrow-up-short' : 'bi bi-arrow-down-short' }),
                    ` ${Math.abs(props.value).toFixed(1)}% vs periodo anterior`,
                ]
            )
        }
    },
})

onMounted(() => {
    const range = computeDateRange(period.value)
    dateFrom.value = range.from
    dateTo.value = range.to
    fetchReport()
})
</script>

<style scoped>
.chart-wrapper {
    position: relative;
    width: 100%;
    flex: 1 1 auto;
    min-height: 320px;
}

.chart-wrapper-sm {
    /* Los gráficos pequeños mantienen una altura fija: no queremos que
       hereden el "flex: 1 1 auto" de .chart-wrapper y crezcan sin control
       (Chart.js con maintainAspectRatio:false necesita un contenedor con
       altura acotada, no solo un mínimo). */
    flex: 0 0 auto;
    height: 180px;
    min-height: 0;
}

.kpi-card .kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.export-period-panel {
    position: absolute;
    top: calc(100% + 4px);
    right: 0;
    z-index: 20;
    width: 220px;
}
</style>