import api from '@/api/axios';

const UserService = {

    getUsers() {
        return api.get('/users');
    },
    createUser(data) {
        return api.post('/users', data)
    },
    getUserDetails(id) {
        return api.get(`/users/${id}`);
    },
    patchUser(id) {
        return api.patch(`/users/${id}/status`);
    },
    updateUser(id, data) {
        return api.patch(`/users/${id}`, data);
    },
    deleteUser(id) {
        return api.delete(`/users/${id}`);
    },
    resetPass(id) {
        return api.patch(`/users/${id}/resp`);
    },
}

export default UserService