<template>
    <navBar></navBar>

    <div class="container p-4">
        <div class="card p-4">
            <Preloader :visible="cargando"></Preloader>
            <div class="d-flex">
                <h1 class="fs-4">Datos del usuario</h1>
                <div class="d-flex gap-2 ms-auto">
                    <button class="btn btn-info" type="button" @click="toggleEdit()">
                        Editar
                    </button>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#changePasswordModal">
                        Cambiar contraseña
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="changePasswordModal" tabindex="-1"
                        aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="changePasswordModalLabel">Cambiar contraseña</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form @submit.prevent="changePassword">
                                        <div class="form-group">
                                            <div class="position-relative">
                                                <label for="currentPassword">Contraseña actual</label>
                                                <input :type="showPassword ? 'text' : 'password'"
                                                    class="form-control pe-5" id="currentPassword" v-model="currentPassword"
                                                    placeholder="Contraseña">
                                                <button type="button" class="btn password-toggle"
                                                    @click="showPassword = !showPassword">
                                                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="position-relative">
                                                <label for="newPassword">Nueva contraseña</label>
                                                <input :type="showNewPassword ? 'text' : 'password'"
                                                    class="form-control pe-5" id="newPassword" v-model="newPassword"
                                                    placeholder="Contraseña">
                                                <button type="button" class="btn password-toggle"
                                                    @click="showNewPassword = !showNewPassword">
                                                    <i :class="showNewPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="position-relative">
                                                <label for="confirmPassword">Confirmar nueva contraseña</label>
                                                <input :type="showConfirmPassword ? 'text' : 'password'"
                                                    class="form-control pe-5" id="confirmPassword" v-model="confirmPassword"
                                                    placeholder="confirmar nueva Contraseña">
                                                <button type="button" class="btn password-toggle"
                                                    @click="showConfirmPassword = !showConfirmPassword">
                                                    <i :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p v-if="passwordError" class="text-danger mt-2 mb-0">
                                            {{ passwordError }}
                                        </p>
                                        <p v-if="passwordSuccess" class="text-success mt-2 mb-0">
                                            {{ passwordSuccess }}
                                        </p>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" :disabled="changingPassword"
                                        @click="changePassword">
                                        {{ changingPassword ? "Guardando..." : "Guardar" }}
                                    </button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end Modal -->
                </div>
            </div>
            <div>
                <div class="d-flex flex-lg-row flex-column gap-3 mx-auto">
                    <div class="w-lg-60 w-md-90 w-100">
                        <form @submit.prevent="updateUser">
                            <div class="form-group">
                                <label for="names">Nombres</label>
                                <input type="text" class="form-control" placeholder="Nombres" :readonly="!isEditing" />
                                <!-- <input type="text" class="form-control" id="names" v-model="user.names" placeholder="Nombres" :readonly="!isEditing"/> -->
                            </div>
                            <div class="form-group">
                                <label for="last_names">Apellidos</label>
                                <input type="text" class="form-control" placeholder="Apellidos"
                                    :readonly="!isEditing" />
                                <!-- <input type="text" class="form-control" id="last_names" v-model="user.last_names" placeholder="Apellidos" :readonly="!isEditing"/> -->
                            </div>
                            <div class="form-group">
                                <label for="dni">Número de DNI</label>
                                <input type="text" class="form-control" placeholder="DNI" readonly />
                            </div>
                            <div class="form-group">
                                <label for="username">Nombre de usuario</label>
                                <input type="text" class="form-control" placeholder="nombre de usuario" readonly />
                            </div>
                            <div v-if="isEditing" class="d-flex gap-3 mt-3 justify-content-center">
                                <button type="submit" class="btn btn-primary" @click="updateUser">{{ loading ?
                                    "Actualizando..." :
                                    "Actualizar" }}</button>
                                <button type="button" class="btn btn-danger" @click="toggleEdit()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Footer />
</template>

<script setup>

import Preloader from '@/components/layout/preloader.vue';
import navBar from '@/components/layout/navBar.vue'
import Footer from '@/components/layout/footer.vue'
import { ref, onBeforeUnmount } from 'vue';
import { Modal } from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import AuthServices from '@/services/AuthService';

const isEditing = ref(false);
const loading = ref(false);
const cargando = ref(false);
const showPassword = ref(false);
const showNewPassword=ref(false);
const showConfirmPassword= ref(false);  
const toggleEdit = () => {
    isEditing.value = !isEditing.value;
};

// --- Cambio de contraseña ---

const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const passwordError = ref('');
const passwordSuccess = ref('');
const changingPassword = ref(false);
let closeModalTimer = null;

const closeModal = () => {

    const modalEl = document.getElementById('changePasswordModal');

    if (!modalEl) return;

    Modal.getInstance(modalEl)?.hide();

};

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

    // por si el modal seguía abierto, lo cerramos ya
    closeModal();

    // limpieza defensiva por si el backdrop ya quedó huérfano
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');

    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

});

const resetChangePasswordForm = () => {

    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
    passwordError.value = '';
    passwordSuccess.value = '';

};

const changePassword = async () => {

    passwordError.value = '';
    passwordSuccess.value = '';

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

        passwordSuccess.value = 'Contraseña actualizada correctamente';

        // Guardamos el timer para poder cancelarlo si el usuario navega
        // a otra página antes de que se cumpla (ver onBeforeUnmount abajo).
        closeModalTimer = setTimeout(() => {

            closeModal();

            resetChangePasswordForm();

        }, 1200);

    } catch (err) {

        passwordError.value = err.response?.data?.message
            || 'No se pudo cambiar la contraseña, verifica la contraseña actual';

    } finally {

        changingPassword.value = false;

    }

};

const updateUser = () => {
    // Implement user update logic here
    loading.value = true;
    console.log('Update user clicked');
    loading.value = false;
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