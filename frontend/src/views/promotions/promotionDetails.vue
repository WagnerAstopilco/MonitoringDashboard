<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
                <h1 class="card-title fw-bold">Detalles de la promoción</h1>
            <div class="card-body w-lg-80 w-md-90 w-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="card-subtitle">Datos de la promoción</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <button v-if="!editable && auth.hasPermission('promotions.edit')" type="button" class="btn btn-warning" @click="toggleEdit">
                            <i class="bi bi-pencil-square"></i>
                            Editar</button>
                        <button v-if="!editable && auth.hasPermission('promotions.delete')" type="button" class="btn btn-danger" @click="deletePromotion">
                            <i class="bi bi-trash3"></i>
                            Eliminar</button>
                    </div>
                </div>

                <form @submit.prevent="updatePromotion">
                    <div class="form-group p-2">
                        <label for="name">Nombre</label>
                        <input id="name" class="form-control" v-model="updatePromotionForm.name" type="text"
                            placeholder="nombre" autocomplete="name" :disabled="!editable" required />
                    </div>
                    <div class="form-group p-2">
                        <label for="description">Descripción</label>
                        <input id="description" class="form-control" v-model="updatePromotionForm.description"
                            type="text" placeholder="descripcion" autocomplete="description" :disabled="!editable"
                            required />
                    </div>
                    <div class="form-group p-2">
                        <label for="cost">Tipo</label>
                        <select name="discount_type" class="form-control" v-model="updatePromotionForm.discount_type"
                            :disabled="!editable">
                            <option value="" selected disabled>Selecciona un tipo</option>
                            <option value="percentage">Porcentaje</option>
                            <option value="fixed">Monto fijo</option>
                        </select>
                    </div>
                    <div class="form-group p-2">
                        <label for="value">Monto</label>
                        <input id="value" class="form-control" v-model="updatePromotionForm.discount_value"
                            type="number" placeholder="precio" autocomplete="value" :disabled="!editable" required />
                    </div>
                    <div class="form-group p-2">
                        <label for="status">Estado</label>
                        <select name="status" class="form-control" v-model="updatePromotionForm.status"
                            :disabled="!editable">
                            <option value="" selected disabled>Selecciona un estado</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    <div class="d-flex gap-4 p-2 flex-wrap">
                        <div class="form-group flex-grow-1">
                            <label for="start_date">Fecha de inicio</label>
                            <input id="start_date" class="form-control" v-model="updatePromotionForm.start_date"
                                type="date" placeholder="precio" autocomplete="start_date" :disabled="!editable"
                                required />
                        </div>
                        <div class="form-group flex-grow-1">
                            <label for="end_date">Fecha de fin</label>
                            <input id="end_date" class="form-control" v-model="updatePromotionForm.end_date"
                                type="date" placeholder="precio" autocomplete="end_date" :disabled="!editable"
                                required />
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label>Imágen</label>
                        <!-- Modo lectura: muestra la imagen guardada en la BD -->
                        <div v-if="!editable">
                            <img v-if="imagePreview" :src="imagePreview" class="img-thumbnail mt-2"
                                style="max-width: 200px;">
                        </div>
                        <!-- Modo edición: muestra el input de archivo y la vista previa de la nueva imagen -->
                        <div v-else>
                            <input id="image" class="form-control" type="file" accept="image/*"
                                @change="handleImageChange" />
                            <img v-if="imagePreview" :src="imagePreview" class="img-thumbnail mt-2"
                                style="max-width: 200px;">
                        </div>
                    </div>
                    <p v-if="error" style="color: red;">
                        {{ error }}
                    </p>
                    <div v-if="editable" class="d-flex gap-2 mt-3 justify-content-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-danger" @click="cancelEdit">Cancelar</button>
                    </div>
                </form>

                <!-- seccion de Servicios -->
                <div class="mt-3">
                    <div v-if="auth.hasPermission('promotions.sync_services')" class="d-flex align-items-center justify-content-between">
                        <h3 class="card-subtitle">Servicios</h3>
                        <button type="button" class="btn btn-success"
                            @click="syncServices">Vincular</button>
                    </div>
                    <div class="table-responsive p-1 ">
                        <DataTable :data="services" :columns="servicesColumns">
                            <template #column-0="props">
                                <div class="text-center">
                                    <input type="checkbox" class="form-check-input"
                                        :checked="selectedServiceIds.includes(props.rowData.id)"
                                        @change="toggleService(props.rowData.id)">
                                </div>
                            </template>
                            <template #column-1="props">
                                <span>
                                    {{ props.rowData.name }}
                                </span>
                            </template>
                            <template #column-2="props">
                                <span>
                                    {{ props.rowData.description }}
                                </span>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4 justify-content-center">
                <button type="button" class="btn btn-primary" @click="goBack">Volver</button>

            </div>
        </div>
    </div>
</template>

<script setup>
import PromotionService from '@/services/PromotionService';
import Preloader from '@/components/layout/preloader.vue';
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DataTable from '@/components/tables/dataTable.vue';
import ServicesService from '@/services/ServicesService';
import { confirmAction, showSuccess,showError } from '@/utils/sweetAlert';
import { useAuthStore } from '@/stores/auth';

const cargando = ref(false);
const editable = ref(false);
const imagePreview = ref(null);
const error = ref("");
const router = useRouter();
const route = useRoute();
const promotion = ref([]);
const services = ref([]);
const selectedServiceIds = ref([]);
const auth=useAuthStore();

const servicesColumns = [
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
        data: 'description',
        title: 'Descripción',
        className: 'd-none d-sm-table-cell text-center'
    }
]
const updatePromotionForm = ref({
    name: '',
    description: '',
    discount_type: '',
    discount_value: '',
    status: '',
    start_date: '',
    end_date: '',
    promotion_image: null,
});

// Guarda la url original de la imagen en BD para poder restaurarla
// si el usuario cancela la edición sin elegir una imagen nueva.
const originalImageUrl = ref(null);

onMounted(async () => {
    getPromotionDetails();

});

const getPromotionDetails = async () => {
    try {
        cargando.value = true;
        const response = await PromotionService.getPromotionDetails(route.params.idpromotion);
        promotion.value = response.data.data;
        selectedServiceIds.value = (promotion.value.services ?? []).map(
            service => service.id
        );

        await getAllServices();
        updatePromotionForm.value = {
            name: promotion.value.name,
            description: promotion.value.description,
            discount_type: promotion.value.discount_type,
            discount_value: promotion.value.discount_value,
            status: promotion.value.status,
            start_date: promotion.value.start_date,
            end_date: promotion.value.end_date,
            promotion_image: null,
        };

        // Ajusta esta propiedad según cómo devuelva tu API la url de la imagen
        originalImageUrl.value = promotion.value.promotion_image;
        imagePreview.value = promotion.value.promotion_image;
    } catch (err) {
        // error.value = 'No se pudo cargar el servicio';
    } finally {
        cargando.value = false;
    }
}

const toggleEdit = () => {
    editable.value = !editable.value;
};

const updatePromotion = async () => {
    try {
        error.value = ''
        const formData = new FormData()

        formData.append('name', updatePromotionForm.value.name)
        formData.append('description', updatePromotionForm.value.description)
        formData.append('discount_type', updatePromotionForm.value.discount_type)
        formData.append('discount_value', updatePromotionForm.value.discount_value)
        formData.append('status', updatePromotionForm.value.status)
        formData.append('start_date', updatePromotionForm.value.start_date)
        formData.append('end_date', updatePromotionForm.value.end_date)

        if (updatePromotionForm.value.promotion_image) {
            formData.append(
                'promotion_image',
                updatePromotionForm.value.promotion_image
            )
        }

        await PromotionService.updatePromotion(route.params.idpromotion, formData)

        editable.value = false;
        await showSuccess('Promoción actualizada correctamente')
        getPromotionDetails();
    } catch (err) {
        error.value = 'No se pudo actualizar el servicio'
    }
}

const handleImageChange = (event) => {
    const file = event.target.files[0]

    if (!file) {
        updatePromotionForm.value.promotion_image = null
        imagePreview.value = originalImageUrl.value
        return
    }

    updatePromotionForm.value.promotion_image = file
    imagePreview.value = URL.createObjectURL(file)
}

const cancelEdit = () => {
    toggleEdit();
    getPromotionDetails();
}

const goBack = () => {
    router.push({ name: 'promotions' })
}
const toggleService = (ServiceId) => {
    const index = selectedServiceIds.value.indexOf(ServiceId);

    if (index === -1) {
        // No estaba seleccionada → agregarla
        selectedServiceIds.value.push(ServiceId);
    } else {
        // Ya estaba seleccionada → quitarla
        selectedServiceIds.value.splice(index, 1);
    }
}

const syncServices = async () => {
    try {
        error.value = '';

        await PromotionService.syncServices(
            route.params.idpromotion,
            selectedServiceIds.value
        );
        await showSuccess('Servicios sincronizados correctamente a la promoción')
        await getPromotionDetails();

    } catch (err) {
        error.value = 'No se pudieron actualizar las promociones';
    }
}

const getAllServices = async () => {
    try {
        const response = await ServicesService.getServices();

        services.value = response.data.data.filter(
            service => service.status === 'active'
        );

    } catch (err) {
        console.error('Error al cargar promociones:', err);
        error.value = 'No se pudieron cargar las promociones';
    }
}

const deletePromotion = async () => {
    const result=await confirmAction('Se eliminara la promoción');
    if(!result.isConfirmed){
        return
    }
    try {
        await PromotionService.deletePromotion(route.params.idpromotion);
        await showSuccess('Pomoción eliminada correctamente')
    } catch (err) {
        await showError('No se puede eliminar la promoción cuando esta activa')
    }

}
</script>