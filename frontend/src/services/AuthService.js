import api from '@/api/axios'

const AuthServices = {

    login(credentials){

    return api.post('/login', credentials)

    },
    loginDemo(){
        return api.post('/demo')
    },

    me(){
        return api.get('/me');
    },

    updateProfile(data){
        return api.patch('/me',data)
    },

    logout(){

        return api.post('/logout')

    },

    changePassword(data){

        return api.post('/change-password', data)
    },

}

export default AuthServices