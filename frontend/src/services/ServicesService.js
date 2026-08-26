import api from '@/api/axios'

const ServicesService = {
    getServices() {
        return api.get('/services');
    },
    getPublicServices() {
        return api.get('/services/publicServices');
    },
    getServiceDetails(id) {
        return api.get(`/services/${id}`);
    },
    getPublicServiceDetails(id) {
        return api.get(`/services/publicServices/${id}`);
    },
    createService(formData) {
    formData.append('_method', 'POST');
        return api.post('/services', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },
    updateService(id, formData) {
    formData.append('_method', 'PATCH');

    return api.post(`/services/${id}`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
},
    syncPromotions(id, promotionIds) {
        return api.patch(`/services/${id}/promotions`, {
            promotions: promotionIds
        });
    },

    deleteService(id) {
        return api.delete(`/services/${id}`)
    },
    patchServiceStatus(id) {
        return api.patch(`/services/${id}/status`);
    },
}

export default ServicesService