import api from '@/api/axios'

const PromotionService = {

    getPromotions() {
        return api.get('/promotions');
    },
    getPublicPromotions() {
        return api.get('/promotions/publicPromotions');
    },
    getPromotionDetails(id) {
        return api.get(`/promotions/${id}`);
    },
    getPublicPromotionDetails(id) {
        return api.get(`/promotions/publicPromotions/${id}`);
    },
    createPromotion(formData) {
        return api.post('/promotions', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },
    updatePromotion(id, formData) {
        formData.append('_method', 'PATCH');
        
        return api.post(`/promotions/${id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },
    syncServices(id, ServiceIds) {
        return api.patch(`/promotions/${id}/services`, {
            services: ServiceIds
        });
    },
    deletePromotion(id) {
        return api.delete(`/promotions/${id}`);
    },
    patchPromotionStatus(id) {
        return api.patch(`/promotions/${id}/status`);
    },
}

export default PromotionService