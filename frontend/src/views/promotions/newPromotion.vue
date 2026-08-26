<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <h1 class="card-title fw-bold">Nueva Promoción</h1>
            <div class="card-body w-lg-80 w-md-90 w-100">
                <h3 class="card-subtitle">Datos de la promoción</h3>
                <form @submit.prevent="newPromotion">
                    <div class="form-group p-2">
                        <label for="name">Nombre</label>
                        <input id="name" class="form-control" v-model="newPromotionForm.name" type="text"
                            placeholder="nombre" autocomplete="name" required />
                    </div>
                    <div class="form-group p-2">
                        <label for="description">Descripción</label>
                        <textarea id="description" class="form-control" v-model="newPromotionForm.description"
                            type="text" placeholder="descripcion" autocomplete="description" required>
                        </textarea>
                    </div>
                    <div class="form-group p-2">
                        <label for="discount_type">Tipo</label>
                        <select name="discount_type" class="form-control" v-model="newPromotionForm.discount_type">
                            <option value="" selected disabled>Selecciona un tipo</option>
                            <option value="percentage">Porcentaje</option>
                            <option value="fixed">Monto fijo</option>
                        </select>
                    </div>
                    <div class="form-group p-2">
                        <label for="value">Monto</label>
                        <input id="value" class="form-control" v-model="newPromotionForm.discount_value" type="number"
                            placeholder="0" autocomplete="0" required />
                    </div>
                    <div class="d-flex gap-4 p-2 flex-wrap">
                        <div class="form-group flex-grow-1">                            
                            <label for="start_date">Fecha de inicio</label>
                            <input id="start_date" class="form-control" v-model="newPromotionForm.start_date" type="date" required />
                        </div>
                        <div class="form-group flex-grow-1">
                            <label for="end_date">Fecha de fin</label>
                            <input id="end_date" class="form-control" v-model="newPromotionForm.end_date" type="date" required />
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="image">Imágen</label>
                        <input id="image" class="form-control" type="file" accept="image/*"
                            @change="handleImageChange" required />
                        <img v-if="imagePreview" :src="imagePreview" class="img-thumbnail mt-2"
                            style="max-width: 200px;">
                    </div>
                    <!-- seccion de Servicios -->
                <div class="mt-3">
                    <h3 class="card-subtitle">Servicios disponibles</h3>
                    <div class="table-responsive p-1">
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
                    <p v-if="error" style="color: red;">
                        {{ error }}
                    </p>
                    <div class="d-flex gap-2 mt-4 justify-content-center">
                        <button type="submit" class="btn btn-primary">Crear</button>
                        <button type="button" class="btn btn-danger" @click="goBack">Volver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import Preloader from '@/components/layout/preloader.vue'
import PromotionService from '@/services/PromotionService';
import { onMounted,ref } from 'vue';
import { useRouter } from 'vue-router';
import DataTable from '@/components/tables/dataTable.vue';
import ServicesService from '@/services/ServicesService';
import { showSuccess } from '@/utils/sweetAlert';

const cargando = ref(false);
const imagePreview = ref(null)
const error = ref("");
const router = useRouter();
const services =ref([]);
const selectedServiceIds=ref([]);

const newPromotionForm = ref({
    name:'',
    description:'',
    discount_type:'',
    discount_value:'',
    start_date:'',
    end_date:'',
    promotion_image: null,
});
const servicesColumns = [
    {
        data: 'id',
        title: 'Selección',
        className: 'text-center'
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

const newPromotion = async () => {
    try {
        error.value = ''
        const formData = new FormData()

        formData.append('name', newPromotionForm.value.name)
        formData.append('description', newPromotionForm.value.description)
        formData.append('discount_type', newPromotionForm.value.discount_type)
        formData.append('discount_value', newPromotionForm.value.discount_value)
        formData.append('start_date', newPromotionForm.value.start_date)
        formData.append('end_date', newPromotionForm.value.end_date)

        if (newPromotionForm.value.promotion_image) {
            formData.append(
                'promotion_image',
                newPromotionForm.value.promotion_image
            )
        }
        selectedServiceIds.value.forEach(id => {
            formData.append('services[]', id)
        })
        await PromotionService.createPromotion(formData)

        // limpiar el formulario
        newPromotionForm.value = {
            name:'',
            description:'',
            discount_type:'',
            discount_value:'',
            start_date:'',
            end_date:'',
            promotion_image: null
        }

        imagePreview.value = null
        await showSuccess('Promoción creada correctamente');
        goBack();
    } catch (err) {
        error.value = 'No se pudo crear la promocion'
    }
}
const handleImageChange = (event) => {
    const file = event.target.files[0]

    if (!file) {
        newPromotionForm.value.promotion_image = null
        imagePreview.value = null
        return
    }

    newPromotionForm.value.promotion_image = file
    imagePreview.value = URL.createObjectURL(file)
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

const getAllServices = async () => {
    try {
        const response = await ServicesService.getServices();

        services.value = response.data.data.filter(
            service => service.status === 'active'
        );

    } catch (err) {
        error.value = 'No se pudieron cargar las promociones';
    }
}

onMounted(async () => {
    await getAllServices();
})
</script>