<template>
    <div class="login-page">
        <div class="login-card">
            <h2 class="d-flex justify-content-center">Recuperar Contraseña</h2>
            <form @submit.prevent="recoveryPassword">
                <div class="form-group">
                    <label for="username">Nombre de usuario</label>

                    <input id="username" class="form-control pe-5" v-model="form.username" type="text"
                        placeholder="Ingrese su nombre de usuario" autocomplete="username" required />
                </div>

                <div class="form-group">
                    <div class="position-relative">
                        <label for="password">Nueva Contraseña</label>
                        <input :type="showPassword ? 'text' : 'password'" class="form-control pe-5"
                            v-model="form.password" placeholder="Nueva Contraseña" />

                        <button type="button" class="btn password-toggle" @click="showPassword = !showPassword">
                            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="position-relative">
                        <label for="password">Confirmar Contraseña</label>
                        <input :type="showPassword ? 'text' : 'password'" class="form-control pe-5"
                            v-model="form.confirm_password" placeholder="Confirmar Contraseña" />

                        <button type="button" class="btn password-toggle" @click="showPassword = !showPassword">
                            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="actions d-flex justify-content-around">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        {{ loading ? "Recuperando..." : "Recuperar" }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="$router.push('/login')">Cancelar</button>
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
import AuthServices from "@/services/AuthService";

const router = useRouter();

const form = reactive({
    username: "",

    password: "",

    confirm_password: "",
});
const showPassword = ref(false);
const loading = ref(false);
const error = ref("");
const success = ref("");

const forgotPassword = async () => {

    error.value = "";
    success.value = "";

    // validación de coincidencia antes de golpear el backend
    if (form.password !== form.confirm_password) {

        error.value = "Las contraseñas no coinciden";

        return;

    }

    if (form.password.length < 8) {

        error.value = "La contraseña debe tener al menos 8 caracteres";

        return;

    }

    try {

        loading.value = true;

        await AuthServices.recoveryPassword({

            username: form.username,

            password: form.password,

            password_confirmation: form.confirm_password,

        });

        success.value = "Contraseña actualizada, ya puedes iniciar sesión";

        setTimeout(() => router.push('/login'), 1500);

    } catch (err) {

        error.value = err.response?.data?.message
            || "No se pudo actualizar la contraseña, verifica el usuario";

    } finally {

        loading.value = false;

    }
};
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
    width: 380px;
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

.success {
    color: #2d8cf0;
    margin-top: 10px;
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
