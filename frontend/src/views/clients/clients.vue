<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <div class="d-flex align-items-center flex-wrap justify-content-between">
                <h1 class="card-title fw-bold">Lista de clientes</h1>
                <!-- Button trigger modal nuevo cliente-->
                <button v-if="auth.hasPermission('clients.create')"type="button" class="btn btn-primary m-md-3" data-bs-toggle="modal"
                    data-bs-target="#newClientModal">
                    <i class="bi bi-plus"></i>
                    Nuevo</button>
            </div>
            
            <!-- modal nuevo usuario -->
            <div class="modal fade" id="newClientModal" tabindex="-1" aria-labelledby="newClientModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="newClientModalLabel">Nuevo Cliente</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="resetForm"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="newClient">
                                <div class="form-group p-2">
                                    <label for="company_name">Nombre de Empresa</label>
                                    <input id="company_name" class="form-control"
                                        :class="{ 'is-invalid': errors.company_name }"
                                        v-model="newClientForm.company_name" type="text" placeholder="Razon social"
                                        autocomplete="company_name" required />
                                    <div v-if="errors.company_name" class="invalid-feedback">
                                        {{ errors.company_name[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="company_ruc">RUC</label>
                                    <input id="company_ruc" class="form-control"
                                        :class="{ 'is-invalid': errors.company_ruc }"
                                        v-model="newClientForm.company_ruc" type="text" placeholder="N° de RUC"
                                        autocomplete="company_ruc" required />
                                    <div v-if="errors.company_ruc" class="invalid-feedback">
                                        {{ errors.company_ruc[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="name">Persona de contacto</label>
                                    <input id="name" class="form-control" :class="{ 'is-invalid': errors.name }"
                                        v-model="newClientForm.name" type="text" placeholder="Nombre del contacto"
                                        autocomplete="name" required />
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="phone">Telefono</label>
                                    <input id="phone" class="form-control" :class="{ 'is-invalid': errors.phone }"
                                        v-model="newClientForm.phone" type="tel" placeholder="N° telefónico"
                                        autocomplete="phone" required />
                                    <div v-if="errors.phone" class="invalid-feedback">
                                        {{ errors.phone[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="email">Correo</label>
                                    <input id="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                                        v-model="newClientForm.email" type="email" placeholder="ejemplo@ejemplo.com"
                                        autocomplete="email" />
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email[0] }}
                                    </div>
                                </div>
                                <div class="form-group p-2">
                                    <label for="address">Dirección</label>
                                    <input id="address" class="form-control" :class="{ 'is-invalid': errors.address }"
                                        v-model="newClientForm.address" type="text" placeholder="Jr. ejemplo 454"
                                        autocomplete="address" />
                                    <div v-if="errors.address" class="invalid-feedback">
                                        {{ errors.address[0] }}
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

            <!-- dataTable de clientes -->
            <div class="table-responsive p-1">
                <DataTable :data="clients" :columns="columns" :show-all-option="false">
                    <template #column-0="props">
                        <span>
                            {{ props.rowData.company_ruc }}
                        </span>
                    </template>
                    <template #column-1="props">
                        <span>
                            {{ props.rowData.company_name }}
                        </span>
                    </template>
                    <template #column-2="props">
                        <span>
                            {{ props.rowData.name }}
                        </span>
                    </template>
                    <template #column-3="props">
                        <span>
                            {{ props.rowData.phone }}
                        </span>
                    </template>
                    <template #column-4="props">
                        <span>
                            {{ props.rowData.email }}
                        </span>
                    </template>
                    <template #column-5="props">
                        <span>
                            {{ props.rowData.address }}
                        </span>
                    </template>
                    <template #column-6="props">
                        <span>
                            <div class="d-flex gap-1 justify-content-center">
                                <!-- Button trigger modal editar cliente-->
                                <button v-if="auth.hasPermission('clients.edit')" type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editClientModal" @click="getClientDetails(props.rowData.id)">
                                    <i class="bi bi-pencil-square"></i>
                                    <span class="d-none d-md-inline">
                                        Editar
                                    </span>
                                </button>
                                <button v-if="auth.hasPermission('clients.delete')" type="button" class="btn btn-md btn-danger"
                                    @click="deleteClient(props.rowData.id)">
                                    <i class="bi bi-trash3"></i>
                                    <span class="d-none d-md-inline">
                                        Eliminar
                                    </span>
                                </button>
                            </div>
                        </span>
                    </template>
                </DataTable>
            </div>

            <!-- modal editar usuario -->
            <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="editClientModalLabel">Editar Usuario</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="updateClient">
                                <div class="form-group p-2">
                                    <label for="company_name">Nombre de Empresa</label>
                                    <input id="company_name" class="form-control"
                                        :class="{ 'is-invalid': errors.company_name }" v-model="client.company_name"
                                        type="text" placeholder="Razon social" autocomplete="company_name" required />
                                    <span v-if="errors.company_name" class="invalid-feedback">
                                        {{ errors.company_name[0] }}
                                    </span>
                                </div>
                                <div class="form-group p-2">
                                    <label for="company_ruc">RUC</label>
                                    <input id="company_ruc" class="form-control"
                                        :class="{ 'is-invalid': errors.company_ruc }" v-model="client.company_ruc"
                                        type="text" placeholder="N° de RUC" autocomplete="company_ruc" required />
                                    <span v-if="errors.company_ruc" class="invalid-feedback">
                                        {{ errors.company_ruc[0] }}
                                    </span>
                                </div>
                                <div class="form-group p-2">
                                    <label for="name">Persona de contacto</label>
                                    <input id="name" class="form-control" :class="{ 'is-invalid': errors.name }"
                                        v-model="client.name" type="text" placeholder="Nombre del contacto"
                                        autocomplete="name" required />
                                    <span v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name[0] }}
                                    </span>
                                </div>
                                <div class="form-group p-2">
                                    <label for="phone">Telefono</label>
                                    <input id="phone" class="form-control" :class="{ 'is-invalid': errors.phone }"
                                        v-model="client.phone" type="tel" placeholder="N° telefónico"
                                        autocomplete="phone" />
                                    <span v-if="errors.phone" class="invalid-feedback">
                                        {{ errors.phone[0] }}
                                    </span>
                                </div>
                                <div class="form-group p-2">
                                    <label for="email">Correo</label>
                                    <input id="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                                        v-model="client.email" type="email" placeholder="ejemplo@ejemplo.com"
                                        autocomplete="email" />
                                    <span v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email[0] }}
                                    </span>
                                </div>
                                <div class="form-group p-2">
                                    <label for="address">Dirección</label>
                                    <input id="address" class="form-control" :class="{ 'is-invalid': errors.address }"
                                        v-model="client.address" type="text" placeholder="Jr. ejemplo 454"
                                        autocomplete="address" />
                                    <span v-if="errors.address" class="invalid-feedback">
                                        {{ errors.address[0] }}
                                    </span>
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
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import DataTable from '@/components/tables/dataTable.vue';
import ClientService from '@/services/ClientService';
import { ref, onMounted, reactive, } from 'vue';
import { Modal } from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { confirmAction, showError, showSuccess } from '@/utils/sweetAlert';
import { useAuthStore } from '@/stores/auth';

// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);
const errors = ref({});
const clients = ref([]);
const auth=useAuthStore();
let closeModalTimer = null;

onMounted(() => {
    getClients();
    clearModal();
})

const getClients = async () => {
    try {
        cargando.value = true;
        const response = await ClientService.getClients();
        clients.value = response.data.data;
    } catch (error) {

    } finally {
        cargando.value = false;
    }
}

// ---------------------------------------------------------------------------
// Section 2: newClient modal
// ---------------------------------------------------------------------------
const newClientForm = reactive({
    company_name: '',
    company_ruc: '',
    name: '',
    phone: '',
    email: '',
    address: '',
});

const newClient = async () => {
    try {
        errors.value = ''
        await ClientService.createClient({
            company_name: newClientForm.company_name,
            company_ruc: newClientForm.company_ruc,
            name: newClientForm.name,
            phone: newClientForm.phone,
            email: newClientForm.email,
            address: newClientForm.address,
        })
        closeModalTimer = setTimeout(() => {
            resetForm();
            closeModal('newClientModal');
        }, 100);
        await showSuccess('Cliente creado correctamente');
        await getClients()
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        } else {
            errors.value = {
                general: ['No se pudo crear el cliente']
            }
        }
    }
}

// ---------------------------------------------------------------------------
// Section 3: dataTable
// ---------------------------------------------------------------------------
const client = ref([]);
const columns = [
    { data: 'company_ruc', title: 'RUC', className: 'text-center' },
    { data: 'company_name', title: 'Empresa', className: 'd-none d-sm-table-cell text-center' },
    { data: 'name', title: 'Contacto', className: 'text-center' },
    { data: 'phone', title: 'Teléfono', className: 'd-none d-sm-table-cell text-center' },
    { data: 'email', title: 'Correo', className: 'd-none d-lg-table-cell text-center text-break' },
    { data: 'address', title: 'Dirección', className: 'd-none d-lg-table-cell  text-center' },
    { data: '', title: 'Acciones', className: 'text-center' },
]

const getClientDetails = async (clientId) => {
    const response = await ClientService.getClientDetails(clientId);
    client.value = response.data.data;
}

const deleteClient = async (clientId) => {
    const result=await confirmAction('Se eliminara el cliente permanentemente')
    if(!result.isConfirmed){
        return
    }
    try{
        await ClientService.deleteClient(clientId);
        showSuccess('Cliente eliminado correctamente');
        getClients();
    }catch(err){
        await showError('No se pudo eliminar al cliente')
    }
}

// ---------------------------------------------------------------------------
// Section 4: updateClient modal
// ---------------------------------------------------------------------------
const updateClient = async () => {
    try {
        errors.value = ''
        await ClientService.updateClient(client.value.id, {
            company_name: client.value.company_name,
            company_ruc: client.value.company_ruc,
            name: client.value.name,
            phone: client.value.phone,
            email: client.value.email,
            address: client.value.address,
        })
        closeModalTimer = setTimeout(() => {
            resetForm()
            closeModal('editClientModal');
        }, 100);
        clearModal();
        await showSuccess('Cliente actualizado correctamente')
        await getClients();
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        } else {
            errors.value = {
                general: ['No se pudo actualizar el cliente']
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
    newClientForm.company_name = '',
        newClientForm.company_ruc = '',
        newClientForm.name = '',
        newClientForm.phone = '',
        newClientForm.email = '',
        newClientForm.address = ''
    errors.value = {}
}

const clearModal=()=>{
    ['newClientModal', 'editClientModal'].forEach((id) => {
        const modalEl = document.getElementById(id);
        if (!modalEl) return;

        modalEl.addEventListener('hide.bs.modal', () => {
            if (modalEl.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });
}
</script>
