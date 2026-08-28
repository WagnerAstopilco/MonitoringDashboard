<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getHomeRouteByRole } from '@/router'
import AuthServices from '@/services/AuthService'

const router = useRouter()
const auth = useAuthStore()

onMounted(async () => {

    try {

        const user = await auth.loginDemo()

        router.replace(
            getHomeRouteByRole(user.role)
        )

    } catch (error) {

        console.error('Error al iniciar la demo:', error)

        router.replace({
            name: 'login'
        })

    }

})
</script>

<template>

    <div class="d-flex flex-column justify-content-center align-items-center vh-100">

        <div class="spinner-border mb-3"></div>

        <p>Iniciando demo...</p>

    </div>

</template>