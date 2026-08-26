import api from '@/api/axios'

const ClientService = {

    getClients(){
        return api.get('/clients');
    },

    createClient(data){
        return api.post('/clients', data);
    },
    getClientDetails(id){
        return api.get(`/clients/${id}`);
    },
    updateClient(id,data){
        return api.patch(`/clients/${id}`, data);
    },
    deleteClient(id){
        return api.delete(`/clients/${id}`);
    },
    searchByRuc(ruc) {
        return api.get('/clients/search', { params: { ruc } });
    },
}

export default ClientService