import api from '@/api/axios'

const AuthServices = {

    login(credentials){

    return api.post('/login', credentials)

    },

    logout(){

        return api.post('/logout')

    },

    recoveryPassword(data){

        return api.post('/forgot-password', data)

    },

    changePassword(data){

        return api.post('/change-password', data)
    },

    recoveryPassword(data){

        return api.post('/reset-password', data)
    }   

}

export default AuthServices