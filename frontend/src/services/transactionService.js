import api from '@/api/axios'

const TransactionService = {

    getTransactions() {
        return api.get('/transactions');
    },
    getTransactionDetails(id) {
        return api.get(`/transactions/${id}`)
    },
    createTransaction(data) {
        return api.post('/transactions', data);
    },
    // Registra un pago (adelanto, parcial o completo) para una transacción existente.
    addPayment(data) {
        return api.post('/transaction-payments', data);
    },
    patchDeliveryStatus(id) {
        return api.patch(`/transactions/${id}/delivery`);
    },
    getReports(params) {
        return api.get('/transactions/reports', { params });
    },
    async exportReport(params, config = {}) {
        return api.get('/transactions/export', {
            params,
            ...config,
        })
    },

}

export default TransactionService