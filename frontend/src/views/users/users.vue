<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex align-items-center flex-wrap justify-content-between">
                <h1 class="card-title fw-bold">Lista de usuarios</h1>
                <!-- Button trigger modal nuevo usuario-->
                <button v-if="auth.hasPermission('users.create')" type="button" class="btn btn-primary m-md-3" data-bs-toggle="modal"
                    data-bs-target="#newUserModal">
                    <i class="bi bi-plus"></i>
                    Nuevo</button>
            </div>

            <!-- modal nuevo usuario -->
            <div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="newUserModalLabel">Nuevo Usuario</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="resetForm"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="newUser">
                                <div class="form-group p-2">
                                    <label for="name">Nombres</label>
                                    <input id="name" class="form-control" :class="{ 'is-invalid': errors.name }"
                                        v-model="form.name" type="text" placeholder="Ingrese sus nombres"
                                        autocomplete="name" required />
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="last_name">Apellidos</label>
                                    <input id="last_name" class="form-control"
                                        :class="{ 'is-invalid': errors.last_name }" v-model="form.last_name" type="text"
                                        placeholder="Ingrese sus apellidos" autocomplete="last_name" required />
                                    <div v-if="errors.last_name" class="invalid-feedback">
                                        {{ errors.last_name[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="dni">DNI</label>
                                    <input id="dni" class="form-control" :class="{ 'is-invalid': errors.dni }"
                                        v-model="form.dni" type="text" placeholder="Ingrese su DNI" autocomplete="dni"
                                        required />
                                    <div v-if="errors.dni" class="invalid-feedback">
                                        {{ errors.dni[0] }}
                                    </div>

                                </div>
                                <div class="form-group p-2">
                                    <label for="username">Nombre de usuario</label>
                                    <input id="username" class="form-control" :class="{ 'is-invalid': errors.username }"
                                        v-model="form.username" type="text" placeholder="Ingrese su nombre de usuario"
                                        autocomplete="username" required />
                                    <div v-if="errors.username" class="invalid-feedback">
                                        {{ errors.username[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="role">Rol</label>
                                    <select name="role" class="form-control" :class="{ 'is-invalid': errors.role }"
                                        v-model="form.role">
                                        <option value="" selected disabled>Selecciona un rol</option>
                                        <option value="admin">Administrador</option>
                                        <option value="employee">Empleado</option>
                                        <option value="visit">Visita</option>
                                    </select>
                                    <div v-if="errors.role" class="invalid-feedback">
                                        {{ errors.role[0] }}
                                    </div>
                                </div>

                                <div class="modal-footer mt-2 justify-content-center gap-2">
                                    <button type="submit" class="btn btn-success">Crear</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                        @click="resetForm">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- tabla de usuarios -->
            <div class="table-responsive p-1">
                <DataTable :data="users" :columns="columns">

                    <template #column-0="props">
                        <span>
                            {{ props.rowData.dni }}
                        </span>
                    </template>
                    <template #column-1="props">
                        <span>
                            {{ props.rowData.name }}
                        </span>
                    </template>
                    <template #column-2="props">
                        <span>
                            {{ props.rowData.last_name }}
                        </span>
                    </template>
                    <template #column-3="props">
                        <span>
                            {{ props.rowData.username }}
                        </span>
                    </template>
                    <template #column-4="props">
                        <span>
                            {{ props.rowData.role }}
                        </span>
                    </template>
                    <template #column-5="props">
                        <span>
                            <button v-if="auth.hasPermission('users.change_status')" type="button" class="btn btn-sm"
                                :class="props.rowData.status === 'active' ? 'btn-success' : 'btn-danger'"
                                @click="changeUserStatus(props.rowData.id)">

                                <i v-if="props.rowData.status === 'active'" class="bi bi-check-circle me-1"></i>
                                <i v-else class="bi bi-x-circle me-1"></i>
                                <span class="d-none d-lg-inline ms-1">
                                    {{ props.rowData.status==='active'?'Activo':'Inactivo' }}
                                </span>
                            </button>
                            <span v-else>{{ props.rowData.status==='active'?'Activo':'Inactivo' }}</span>
                        </span>
                    </template>
                    <template #column-6="props">
                        <span class="d-flex gap-1 justify-content-center">
                            <!-- Button trigger modal editar usuario-->
                            <button v-if="auth.hasPermission('users.edit')" type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editUserModal" @click="getUserDetails(props.rowData.id)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button v-if="auth.hasPermission('users.delete')"type="button" class="btn btn-sm btn-danger" @click="deleteUser(props.rowData.id)">
                                <i class="bi bi-trash3"></i>
                            </button>
                            <button v-if="auth.hasPermission('users.reset_password')" type="button" class="btn btn-sm btn-primary" @click="resetPass(props.rowData.id)">
                                <i class="bi bi-hurricane"></i>
                            </button>
                        </span>
                    </template>
                </DataTable>
            </div>

            <!-- modal editar usuario -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="editUserModalLabel">Editar Usuario</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="updateUser">
                                <div class="form-group p-2">
                                    <label for="name">Nombres</label>
                                    <input id="name" class="form-control" :class="{ 'is-invalid': errors.name }"
                                        v-model="user.name" type="text" placeholder="Ingrese sus nombres"
                                        autocomplete="name" required />
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="last_name">Apellidos</label>
                                    <input id="last_name" class="form-control"
                                        :class="{ 'is-invalid': errors.last_name }" v-model="user.last_name" type="text"
                                        placeholder="Ingrese sus apellidos" autocomplete="last_name" required />
                                    <div v-if="errors.last_name" class="invalid-feedback">
                                        {{ errors.last_name[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="dni">DNI</label>
                                    <input id="dni" class="form-control" :class="{ 'is-invalid': errors.dni }"
                                        v-model="user.dni" type="text" placeholder="Ingrese su DNI" autocomplete="dni"
                                        required />
                                    <div v-if="errors.dni" class="invalid-feedback">
                                        {{ errors.dni[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="username">Nombre de usuario</label>
                                    <input id="username" class="form-control" :class="{ 'is-invalid': errors.username }"
                                        v-model="user.username" type="text" placeholder="Ingrese su nombre de usuario"
                                        autocomplete="username" required />
                                    <div v-if="errors.username" class="invalid-feedback">
                                        {{ errors.username[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="role">Rol</label>
                                    <select name="role" class="form-control" :class="{ 'is-invalid': errors.role }"
                                        v-model="user.role">
                                        <option value="" selected disabled>Selecciona un rol</option>
                                        <option value="admin">Administrador</option>
                                        <option value="employee">Empleado</option>
                                        <option value="visit">Visita</option>
                                    </select>
                                    <div v-if="errors.role" class="invalid-feedback">
                                        {{ errors.role[0] }}
                                    </div>
                                </div>

                                <div class="modal-footer mt-2 justify-content-center gap-2">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <button type="button" class="btn btn-danger"
                                        data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- fin modal editar usuario -->
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import UserService from '@/services/UserService';
import { ref, onBeforeUnmount, onMounted, reactive } from 'vue';
import DataTable from '@/components/tables/dataTable.vue';
import { Modal } from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { showSuccess,showError, confirmAction } from '@/utils/sweetAlert';
import { useAuthStore } from '@/stores/auth';


// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);
const users = ref([]);
const errors = ref({});
const auth=useAuthStore();
let closeModalTimer = null;

onMounted(() => {
    getUsers();

    clearModal()
})

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

const getUsers = async () => {
    try {
        cargando.value = true;
        const response = await UserService.getUsers();
        users.value = response.data.data;
    } catch (error) {
        // await showError('No se encontraron usuarios en este momento')
    } finally {
        cargando.value = false;
    }
}

// ---------------------------------------------------------------------------
// Section 2: newUser modal
// ---------------------------------------------------------------------------
const form = reactive({
    name: '',
    last_name: '',
    dni: '',
    username: '',
    role: '',

})

const newUser = async () => {
    try {
        errors.value = {}
        await UserService.createUser({
            name: form.name,
            last_name: form.last_name,
            dni: form.dni,
            username: form.username,
            role: form.role,
        })
        closeModalTimer = setTimeout(() => {
            resetForm();
            closeModal('newUserModal');
        }, 100);
        clearModal()
        await showSuccess('Usuario creado correctamente')
        await getUsers()
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        } else {
            errors.value = {
                general: ['No se pudo crear el usuario']
            }
        }
    }
}

const clearModal=()=>{
    ['newUserModal', 'editUserModal'].forEach((id) => {
        const modalEl = document.getElementById(id);
        if (!modalEl) return;

        modalEl.addEventListener('hide.bs.modal', () => {
            if (modalEl.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });
}
// ---------------------------------------------------------------------------
// Section 3: dataTable
// ---------------------------------------------------------------------------
const user = ref([]);
const columns = [
    { data: 'dni', title: 'DNI', className: 'text-center' },
    { data: 'name', title: 'Nombres', className: 'd-none d-md-table-cell text-center' },
    { data: 'last_name', title: 'Apellidos', className: 'd-none d-md-table-cell text-center' },
    { data: 'username', title: 'Usuario', className: 'text-center' },
    { data: 'role', title: 'Rol', className: 'd-none d-sm-table-cell text-center' },
    { data: 'status', title: 'Estado', className: 'text-center', },
    { data: '', title: 'Acciones', className: 'text-center', }
];

const getUserDetails = async (userId) => {
    cargando.value = true
    const response = await UserService.getUserDetails(userId);
    user.value = response.data.data;
    cargando.value = false;
}

const changeUserStatus = async (userId) => {
    const result=await confirmAction('Se modificara el estado del usuario seleccionado')

    if(!result.isConfirmed){
        return
    }

    try {
        await UserService.patchUser(userId);
        await showSuccess('Se modifico el estado del usuario con exito');
        await getUsers();
    } catch (err) {
        await showError('No se pudo modificar el estado del usuario')
    }
}

const deleteUser = async (userId) => {
    const result =await confirmAction('Esta accion no se puede deshacer.','¿Desea eliminar el usuario?');

    if(!result.isConfirmed){
        return
    }
    try{
        await UserService.deleteUser(userId);
        await showSuccess('Se elimino el usuario correctamente')
        getUsers();
    }catch(error){
        await showError('No se pudo eliminar el usuario')
    }
}

const resetPass = async (userId) => {
    const result=await confirmAction('Se reestablecera la contraseña del usuario')

    if(!result.isConfirmed){
        return
    }
    try{
        await UserService.resetPass(userId);
        await showSuccess('Se reestablecio la contraseña del usuario con exito')
    }catch(err){
        await showError('No se pudo reestablecer la contraseña del usuario')
    }
}

// ---------------------------------------------------------------------------
// Section 4: updateUser modal
// ---------------------------------------------------------------------------
const updateUser = async () => {
    try {
        errors.value = {}
        await UserService.updateUser(user.value.id, {
            name: user.value.name,
            last_name: user.value.last_name,
            dni: user.value.dni,
            username: user.value.username,
            role: user.value.role,
        })
        closeModalTimer = setTimeout(() => {
            resetForm()
            closeModal('editUserModal');
        }, 100);
        await showSuccess('Se actualizo los datos del usuario con éxito')
        await getUsers();
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        } else {
            errors.value = {
                general: ['No se pudo actualizar el usuario']
            }
        }
    }
}

// ---------------------------------------------------------------------------
// Section 5: common methods
// ---------------------------------------------------------------------------
const closeModal = (idModal) => {
    const modalEl = document.getElementById(idModal);
    if (!modalEl) return;
    Modal.getInstance(modalEl)?.hide();
};

const resetForm = () => {
    form.name = ''
    form.last_name = ''
    form.dni = ''
    form.username = ''
    form.role = ''
    errors.value = {}
}
</script>