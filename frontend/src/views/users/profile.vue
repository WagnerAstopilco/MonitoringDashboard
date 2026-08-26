<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex align-items-center flex-wrap justify-content-between mb-3">
                <h1>Perfil de usuario</h1>
                <div class="d-flex flex-wrap gap-2">
                    <button v-if="!isEditing" class="btn btn-info" type="button" @click="toggleEdit()">
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </button>
                    <!-- Button trigger modal -->
                    <button  v-if="!isEditing" type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#changePasswordModal">
                        Cambiar contraseña
                    </button>

                    <!-- Modal change Pass -->
                    <div class="modal fade" id="changePasswordModal" tabindex="-1"
                        aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="modal-title" id="changePasswordModalLabel">Cambiar contraseña</h2>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        @click="resetChangePasswordForm"></button>
                                </div>
                                <div class="modal-body">
                                    <form @submit.prevent="changePassword">
                                        <div class="form-group p-2">
                                            <div class="position-relative">
                                                <label for="currentPassword">Contraseña actual</label>
                                                <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                                    id="currentPassword" v-model="currentPassword"
                                                    placeholder="Contraseña">
                                                <button type="button" class="btn password-toggle"
                                                    @click="showPassword = !showPassword">
                                                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group p-2">
                                            <div class="position-relative">
                                                <label for="newPassword">Nueva contraseña</label>
                                                <input :type="showNewPassword ? 'text' : 'password'"
                                                    class="form-control" id="newPassword" v-model="newPassword"
                                                    placeholder="Contraseña">
                                                <button type="button" class="btn password-toggle"
                                                    @click="showNewPassword = !showNewPassword">
                                                    <i :class="showNewPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group p-2">
                                            <div class="position-relative">
                                                <label for="confirmPassword">Confirmar nueva contraseña</label>
                                                <input :type="showConfirmPassword ? 'text' : 'password'"
                                                    class="form-control" id="confirmPassword" v-model="confirmPassword"
                                                    placeholder="confirmar nueva Contraseña">
                                                <button type="button" class="btn password-toggle"
                                                    @click="showConfirmPassword = !showConfirmPassword">
                                                    <i
                                                        :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p v-if="passwordError" class="text-danger mt-2 mb-0">
                                            {{ passwordError }}
                                        </p>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" :disabled="changingPassword"
                                        @click="changePassword">
                                        {{ changingPassword ? "Guardando..." : "Guardar" }}
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                        @click="resetChangePasswordForm">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <div class="w-lg-60 w-md-90 w-100">
                    <h4>Datos personales</h4>
                    <form @submit.prevent="updateProfile">
                        <div class="form-group p-2">
                            <label for="names">Nombres</label>
                            <input type="text" class="form-control" id="names" v-model="user.name" placeholder="Nombres"
                                :disabled="!isEditing" />
                        </div>
                        <div class="form-group p-2">
                            <label for="last_names">Apellidos</label>

                            <input type="text" class="form-control" id="last_names" v-model="user.last_name"
                                placeholder="Apellidos" :disabled="!isEditing" />
                        </div>
                        <div class="form-group p-2">
                            <label for="dni">Número de DNI</label>
                            <input type="text" class="form-control" id="dni" v-model="user.dni" placeholder="DNI"
                                :disabled="!isEditing" />
                        </div>
                        <div class="form-group p-2">
                            <label for="username">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username" v-model="user.username"
                                placeholder="nombre de usuario" :disabled="!isEditing" />
                        </div>
                        <div v-if="isEditing" class="d-flex gap-3 mt-3 justify-content-center">
                            <button type="submit" class="btn btn-primary">{{ loading ?
                                "Actualizando..." :
                                "Actualizar" }}</button>
                            <button type="button" class="btn btn-danger" @click="toggleEdit()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue';
import { ref, onBeforeUnmount, onMounted } from 'vue';
import { Modal } from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import AuthServices from '@/services/AuthService';
import { useRoute } from 'vue-router';
import UserService from '@/services/UserService';
import { showSuccess, showError } from '@/utils/sweetAlert';



// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);

onMounted(() => {
    loadProfile();
    ['changePasswordModal'].forEach((id) => {
        const modalEl = document.getElementById(id);
        if (!modalEl) return;

        modalEl.addEventListener('hide.bs.modal', () => {
            if (modalEl.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });
});

// Red de seguridad: si el usuario navega a otra ruta mientras el modal
// sigue abierto (o mientras closeModalTimer está pendiente), Vue va a
// destruir #changePasswordModal, pero el <div class="modal-backdrop">
// que Bootstrap agregó directo al <body> NO es parte del árbol de Vue,
// así que nadie lo quita. Eso deja al <body> con clases/estilos de
// "modal abierto" que rompen la interactividad de otros componentes
// de Bootstrap en la página (como el dropdown del navbar).
onBeforeUnmount(() => {
    if (closeModalTimer) {
        clearTimeout(closeModalTimer);
    }
    resetChangePasswordForm();
    closeModal();

    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

});

const loadProfile = async () => {
    try{
        cargando.value = true;
        const response = await AuthServices.me();
        user.value = response.data.data;
        cargando.value = false;
    }catch(err){
        showError('No se encontro al usuario');
    }
}

// ---------------------------------------------------------------------------
// Section 2: change pass
// ---------------------------------------------------------------------------
const showPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const passwordError = ref('');
const changingPassword = ref(false);
let closeModalTimer = null;

const changePassword = async () => {

    passwordError.value = '';


    if (!currentPassword.value || !newPassword.value || !confirmPassword.value) {
        passwordError.value = 'Completa todos los campos';
        return;
    }

    if (newPassword.value !== confirmPassword.value) {
        passwordError.value = 'Las contraseñas nuevas no coinciden';
        return;
    }

    if (newPassword.value.length < 8) {
        passwordError.value = 'La nueva contraseña debe tener al menos 8 caracteres';
        return;
    }

    if (newPassword.value === currentPassword.value) {
        passwordError.value = 'La nueva contraseña debe ser distinta a la actual';
        return;
    }

    try {
        changingPassword.value = true;

        await AuthServices.changePassword({
            current_password: currentPassword.value,
            password: newPassword.value,
            password_confirmation: confirmPassword.value,
        });

        // Guardamos el timer para poder cancelarlo si el usuario navega
        // a otra página antes de que se cumpla (ver onBeforeUnmount abajo).
        closeModalTimer = setTimeout(() => {

            closeModal();

            resetChangePasswordForm();

        }, 1200); 
        await showSuccess('Se modifico la contraseña correctamente')

    } catch (err) {
        passwordError.value = err.response?.data?.message
            || 'No se pudo cambiar la contraseña, verifica la contraseña actual';
    }

};

const resetChangePasswordForm = () => {
    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
    passwordError.value = '';
    showPassword.value = false
    showNewPassword.value = false
    showConfirmPassword.value = false
    passwordError.value = ''
    changingPassword.value = false
};

const closeModal = () => {
    const modalEl = document.getElementById('changePasswordModal');
    if (!modalEl) return;
    Modal.getInstance(modalEl)?.hide();
};

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
};

// ---------------------------------------------------------------------------
// Section 3: update user
// ---------------------------------------------------------------------------
const route = useRoute();
const user = ref([]);
const error = ref("");
const isEditing = ref(false);
const loading = ref(false);

const updateProfile = async () => {
    try {
        error.value = ''
        await AuthServices.updateProfile({
            name: user.value.name,
            last_name: user.value.last_name,
            dni: user.value.dni,
            username: user.value.username,
        })
        toggleEdit();
        await loadProfile();
        await showSuccess('Se actualizó el usuario correctamente');
    } catch (err) {
        console.log(err)
        await showError('No se pudo actualizar los datos del usuario');
    }
};
</script>

<style scoped>
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