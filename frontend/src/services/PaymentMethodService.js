import api from '@/api/axios'

const PaymentMethodService = {

    getPaymentMethods() {
        return api.get('/payment-methods');
    },

}

export default PaymentMethodService
