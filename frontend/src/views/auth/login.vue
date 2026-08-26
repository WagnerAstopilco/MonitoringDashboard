<template>
	<div class="login-page">
		<div class="login-card">
			<h2 class="d-flex justify-content-center">Iniciar Sesión</h2>
			<form @submit.prevent="login">
				<div class="form-group">
					<label for="username">Nombre de usuario</label>

					<input id="username" class="form-control pe-5"  v-model="form.username" type="text" placeholder="Ingrese su nombre de usuario"
						autocomplete="username" required />
				</div>

				<div class="position-relative">
					<label for="password">Contraseña</label>
					<input :type="showPassword ? 'text' : 'password'" class="form-control pe-5" v-model="form.password"
						placeholder="Contraseña">

					<button type="button" class="btn password-toggle" @click="showPassword = !showPassword">
						<i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
					</button>

				</div>

				<div class="form-group-inline pt-2">
					<label>
						<input type="checkbox" v-model="form.remember" />

						Recordarme
					</label>
				</div>

				<div class="actions">
					<button type="submit" :disabled="loading">
						{{ loading ? "Iniciando sesión..." : "Iniciar sesión" }}
					</button>
				</div>

				<p v-if="error" class="error">
					{{ error }}
				</p>
			</form>
		</div>
	</div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from '@/stores/auth';
import { getHomeRouteByRole } from '@/router';

const router = useRouter();

const auth = useAuthStore()

const form = reactive({

    username:'',

    password:'',

    remember:false

})
const showPassword = ref(false);
const loading = ref(false);
const error = ref("");

const login = async()=>{

    try{

        loading.value=true

        error.value=''


        const user = await auth.login({

            username: form.username,

            password: form.password

        }, form.remember)
        router.push(getHomeRouteByRole(user.role))


    }catch(err){

        error.value = 'Usuario o contraseña incorrectos'

    }finally{

        loading.value=false

    }

}
</script>

<style scoped>
.login-page {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 100vh;
	background: #f5f7fb;
}

.login-card {
	width: 360px;
	padding: 24px;
	background: #fff;
	border-radius: 8px;
	box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

.login-card h2 {
	margin: 0 0 16px;
}

.form-group {
	margin-bottom: 12px;
}

.form-group label {
	display: block;
	font-size: 14px;
	margin-bottom: 6px;
}

.form-group input {
	width: 100%;
	padding: 8px 10px;
	border: 1px solid #dcdfe6;
	border-radius: 4px;
}

.form-group-inline {
	margin-bottom: 12px;
}

.actions {
	margin-top: 8px;
}

.actions button {
	width: 100%;
	padding: 10px;
	background: #2d8cf0;
	color: #fff;
	border: none;
	border-radius: 4px;
}

.error {
	color: #ef4b4b;
	margin-top: 10px;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-18%);
    border: none;
    background: transparent;
    z-index: 3;
}
</style>
