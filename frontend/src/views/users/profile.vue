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
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        Cambiar contraseña
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
                        aria-hidden="true">
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
                                            <label for="currentPassword">Contraseña actual</label>
                                            <input type="password" class="form-control" id="currentPassword" v-model="currentPassword" />
                                        </div>
                                        <div class="form-group">
                                            <label for="newPassword">Nueva contraseña</label>
                                            <input type="password" class="form-control" id="newPassword" v-model="newPassword" />
                                        </div>
                                        <div class="form-group">
                                            <label for="confirmPassword">Confirmar nueva contraseña</label>
                                            <input type="password" class="form-control" id="confirmPassword" v-model="confirmPassword" />
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" @click="changePassword">Guardar</button>
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
                                <button type="submit" class="btn btn-primary" @click="updateUser">{{ loading ? "Actualizando..." :
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
import { ref } from 'vue';

const isEditing = ref(false);
const loading = ref(false);
const cargando = ref(false);

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
};
const changePassword = () => {
    // Implement password change logic here
    console.log('Change password clicked');
};
const updateUser = () => {
    // Implement user update logic here
    loading.value = true;
    console.log('Update user clicked');
    loading.value = false;
};

</script>