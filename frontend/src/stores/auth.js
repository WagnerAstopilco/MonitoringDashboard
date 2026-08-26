import { defineStore } from 'pinia'
import AuthServices from '@/services/AuthService'
import { setAuth, getToken, getUser, clearAuth } from '@/utils/authStorage'

export const useAuthStore = defineStore('auth', {

    state: () => ({
        user: getUser(),
        token: getToken(),
        loading: false,
        error: null
    }),

    getters: {

        isAuthenticated: (state) => !!state.token,

        permissions: (state) => {
            return state.user?.permissions ?? []
        },

        hasPermission: (state) => {
            return (permission) => {
                return state.user?.permissions?.includes(permission) ?? false
            }
        },

        hasAnyPermission: (state) => {
            return (permissions) => {
                return permissions.some(permission =>
                    state.user?.permissions?.includes(permission)
                )
            }
        },

        hasAllPermissions: (state) => {
            return (permissions) => {
                return permissions.every(permission =>
                    state.user?.permissions?.includes(permission)
                )
            }
        }

    },

    actions: {

        setUser(user) {
            this.user = user
        },

        setToken(token) {
            this.token = token
        },

        async logout() {

            try {

                await AuthServices.logout()

            } finally {

                this.user = null

                this.token = null

                clearAuth()

            }

        },

        async login(credentials, remember = false) {

            try {

                this.loading = true

                const response = await AuthServices.login(credentials)

                this.token = response.data.access_token

                this.user = response.data.user

                setAuth(response.data.access_token, response.data.user, remember)

                return response.data.user


            } catch (error) {

                throw error

            } finally {

                this.loading = false

            }

        }


    }

})
