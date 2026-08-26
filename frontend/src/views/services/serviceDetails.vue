<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <h1 class="fw-bold card-title">Detalles del servicio</h1>
            <div class="card-body w-lg-80 w-md-90 w-100">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <h3 Class="card-subtitle">Datos del servicio</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <button v-if="!editable && auth.hasPermission('services.edit')" type="button" class="btn btn-warning" @click="toggleEdit">
                            <i class="bi bi-pencil-square"></i>
                            Editar</button>
                        <button v-if="!editable && auth.hasPermission('services.delete')" type="button" class="btn btn-danger" @click="deleteService">
                            <i class="bi bi-trash3"></i>
                            Eliminar</button>
                    </div>
                </div>

                <form @submit.prevent="updateService">
                    <div class="form-group p-2">
                        <label for="name">Nombre</label>
                        <input id="name" class="form-control" :class="{ 'is-invalid': errors.name }"
                            v-model="updateServiceForm.name" type="text" placeholder="nombre" autocomplete="name"
                            :disabled="!editable" required />
                        <div v-if="errors.name" class="invalid-feedback">
                            {{ errors.name[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="description">Descripción</label>
                        <textarea id="description" class="form-control" :class="{ 'is-invalid': errors.description }"
                            v-model="updateServiceForm.description" type="text" placeholder="descripcion"
                            autocomplete="description" :disabled="!editable" required></textarea>
                        <div v-if="errors.description" class="invalid-feedback">
                            {{ errors.description[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="cost">Costo</label>
                        <input id="cost" class="form-control" :class="{ 'is-invalid': errors.cost }"
                            v-model="updateServiceForm.cost" type="number" placeholder="costo" autocomplete="cost"
                            :disabled="!editable" required />
                        <div v-if="errors.cost" class="invalid-feedback">
                            {{ errors.cost[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="price">Precio unitario</label>
                        <input id="price" class="form-control" :class="{ 'is-invalid': errors.price }"
                            v-model="updateServiceForm.price" type="number" placeholder="precio" autocomplete="price"
                            :disabled="!editable" required />
                        <div v-if="errors.price" class="invalid-feedback">
                            {{ errors.price[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="status">Estado</label>
                        <select name="status" class="form-control" :class="{ 'is-invalid': errors.status }"
                            v-model="updateServiceForm.status" :disabled="!editable">
                            <option value="" selected disabled>Selecciona un estado</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        <div v-if="errors.status" class="invalid-feedback">
                            {{ errors.status[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label>Imágen</label>
                        <!-- Modo lectura: muestra la imagen guardada en la BD -->
                        <div v-if="!editable">
                            <img v-if="imagePreview" :src="imagePreview" class="img-thumbnail mt-2"
                                style="max-width: 200px;">
                            <p class="fst-italic" v-else>El servicio no tiene imágen vinculada</p>
                        </div>

                        <!-- Modo edición: muestra el input de archivo y la vista previa de la nueva imagen -->
                        <div v-else>
                            <input id="image" class="form-control pe-5" type="file" accept="image/*"
                                @change="handleImageChange" />
                            <img v-if="imagePreview" :src="imagePreview" class="img-thumbnail mt-2"
                                style="max-width: 200px;">
                        </div>
                        <div v-if="errors.service_image" class="invalid-feedback">
                            {{ errors.service_image[0] }}
                        </div>
                    </div>
                    <div v-if="editable" class="d-flex gap-2 mt-3 justify-content-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-danger" @click="cancelEdit">Cancelar</button>
                    </div>
                </form>

                <!-- seccion de promociones -->
                <div class="mt-3">
                    <div class="d-flex align-items-center flex-wrap justify-content-between">
                        <h3 class="card-subtitle">Promociones</h3>
                        <button v-if="auth.hasPermission('services.sync_promotions')" type="button" class="btn btn-success" @click="syncPromotions">Vincular</button>
                    </div>
                    <div class="table-responsive p-1">
                        <DataTable :data="promotions" :columns="promotionsColumns">
                            <template #column-0="props">
                                <div class="text-center">
                                    <input type="checkbox" class="form-check-input"
                                        :checked="selectedPromotionIds.includes(props.rowData.id)"
                                        @change="togglePromotion(props.rowData.id)">
                                </div>
                            </template>
                            <template #column-1="props">
                                <span>
                                    {{ props.rowData.name }}
                                </span>
                            </template>
                            <template #column-2="props">
                                <span>
                                    {{ props.rowData.discount_type }}
                                </span>
                            </template>
                            <template #column-3="props">
                                <span>
                                    {{ props.rowData.discount_value }}
                                </span>
                            </template>
                            <template #column-4="props">
                                <span>
                                    {{ props.rowData.end_date }}
                                </span>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 justify-content-center">
                <button type="button" class="btn btn-primary" @click="goBack">Volver</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import ServicesService from '@/services/ServicesService';
import Preloader from '@/components/layout/preloader.vue';
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DataTable from '@/components/tables/dataTable.vue';
import PromotionsService from '@/services/PromotionService';
import { confirmAction, showError, showSuccess, showWarning } from '@/utils/sweetAlert';
import { useAuthStore } from '@/stores/auth';

const cargando = ref(false);
const editable = ref(false);
const imagePreview = ref(null);
const error = ref("");
const errors = ref({});
const router = useRouter();
const route = useRoute();
const service = ref([]);
const promotions = ref([]);
const selectedPromotionIds = ref([]);
const auth=useAuthStore();

const promotionsColumns = [
    {
        data: 'id',
        title: 'Selección',
        className: 'text-center',
        width: '80px'
    },
    {
        data: 'name',
        title: 'Nombre',
        className: 'text-center'
    },
    {
        data: 'discount_type',
        title: 'Tipo',
        className: 'd-none d-sm-table-cell text-center'
    },
    {
        data: 'discount_value',
        title: 'Valor',
        className: 'text-center'
    },
    {
        data: 'end_date',
        title: 'Fecha de fin',
        className: 'd-none d-sm-table-cell text-center'
    }
]
const updateServiceForm = ref({
    name: '',
    description: '',
    cost: '',
    price: '',
    status: '',
    service_image: null,
});

// Guarda la url original de la imagen en BD para poder restaurarla
// si el usuario cancela la edición sin elegir una imagen nueva.
const originalImageUrl = ref(null);

onMounted(async () => {
    getServiceDetails();
});

const getServiceDetails = async () => {
    try {
        cargando.value = true;
        const response = await ServicesService.getServiceDetails(route.params.idservice);
        service.value = response.data.data;
        selectedPromotionIds.value = (service.value.promotions ?? []).map(
            promotion => promotion.id
        );
        await getAllPromotions();
        updateServiceForm.value = {
            name: service.value.name,
            description: service.value.description,
            cost: service.value.cost,
            price: service.value.price,
            status: service.value.status,
            service_image: null,
        };

        // Ajusta esta propiedad según cómo devuelva tu API la url de la imagen
        originalImageUrl.value = service.value.service_image;
        imagePreview.value = service.value.service_image;
    } catch (err) {
        // await showError('No se encontro el servicio.')
    } finally {
        cargando.value = false;
    }
}

const toggleEdit = () => {
    editable.value = !editable.value;
};

const updateService = async () => {
    try {
        errors.value = {}
        const formData = new FormData()

        formData.append('name', updateServiceForm.value.name)
        formData.append('description', updateServiceForm.value.description)
        formData.append('cost', updateServiceForm.value.cost)
        formData.append('price', updateServiceForm.value.price)
        formData.append('status', updateServiceForm.value.status)

        if (updateServiceForm.value.service_image) {
            formData.append(
                'service_image',
                updateServiceForm.value.service_image
            )
        }

        await ServicesService.updateService(route.params.idservice, formData)

        editable.value = false;
        await showSuccess('Servicio actualizado correctamente');
        getServiceDetails();
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors
        } else {
            errors.value = {
                general: ['No se pudo actualizar el servicio']
            }
        }
    }
}

const handleImageChange = (event) => {
    const file = event.target.files[0]

    if (!file) {
        updateServiceForm.value.service_image = null
        imagePreview.value = originalImageUrl.value
        return
    }

    updateServiceForm.value.service_image = file
    imagePreview.value = URL.createObjectURL(file)
}

const cancelEdit = () => {
    toggleEdit();
    getServiceDetails();
}

const goBack = () => {
    router.push({ name: 'services' })
}
const togglePromotion = (promotionId) => {
    const index = selectedPromotionIds.value.indexOf(promotionId);

    if (index === -1) {
        // No estaba seleccionada → agregarla
        selectedPromotionIds.value.push(promotionId);
    } else {
        // Ya estaba seleccionada → quitarla
        selectedPromotionIds.value.splice(index, 1);
    }
}

const syncPromotions = async () => {
    try {
        error.value = '';

        await ServicesService.syncPromotions(
            route.params.idservice,
            selectedPromotionIds.value
        );
        await showSuccess('Promociones sincronizadas correctamente al servicio')
        await getServiceDetails();

    } catch (err) {
        await showError('No se pudo sincronizar las promociones');
    } 
}

const getAllPromotions = async () => {
    const response = await PromotionsService.getPromotions();

    promotions.value = response.data.data.filter(
        promotion => promotion.status === 'active'
    );
}

const deleteService = async () => {
    const result=await confirmAction('El servicio se eliminará permanentemente');
    if(!result.isConfirmed){
        return
    }
    try {
        await ServicesService.deleteService(route.params.idservice);
        await showSuccess('Servicio eliminado correctamente');
        goBack();
    } catch (err) {
        // console.error('Error al sincronizar promociones:', err);
        // console.error('Respuesta:', err.response?.data);
        await showError('No se pud eliminar el servicio');
    }
}
</script>