<template>
    <Preloader :visible="cargando"></Preloader>
    <div class="container-fluid py-3 px-4">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            <h1 class="card-title fw-bold">Nuevo servicio</h1>
            <div class="card-body w-lg-80 w-md-90 w-100">
                <h3 class="card-subtitle">Datos del servicio</h3>
                <form @submit.prevent="newService">
                    <div class="form-group p-2">
                        <label for="name">Nombre</label>
                        <input id="name" class="form-control" :class="{ 'is-invalid': errors.name }"
                            v-model="newServiceForm.name" type="text" placeholder="nombre" autocomplete="name"
                            required />
                        <div v-if="errors.name" class="invalid-feedback">
                            {{ errors.name[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="description">Descripción</label>
                        <textarea id="description" class="form-control" :class="{ 'is-invalid': errors.description }"
                            v-model="newServiceForm.description" type="text" placeholder="descripcion"
                            autocomplete="description" required />
                        <div v-if="errors.description" class="invalid-feedback">
                            {{ errors.description[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="cost">Costo</label>
                        <input id="cost" class="form-control" :class="{ 'is-invalid': errors.cost }"
                            v-model="newServiceForm.cost" type="number" placeholder="0" autocomplete="0"
                            required />
                        <div v-if="errors.cost" class="invalid-feedback">
                            {{ errors.cost[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="price">Precio unitario</label>
                        <input id="price" class="form-control" :class="{ 'is-invalid': errors.price }"
                            v-model="newServiceForm.price" type="number" placeholder="0" autocomplete="0"
                            required />
                        <div v-if="errors.price" class="invalid-feedback">
                            {{ errors.price[0] }}
                        </div>
                    </div>
                    <div class="form-group p-2">
                        <label for="image">Imágen</label>
                        <input id="image" class="form-control" type="file" accept="image*" @change="handleImageChange" required />
                        <img v-if="imagePreview" :src="imagePreview" class="img-thumbnail mt-2"
                            style="max-width: 200px;">
                        <div v-if="errors.service_image" class="invalid-feedback">
                            {{ errors.service_image[0] }}
                        </div>
                    </div>
                    <!-- seccion de promociones -->
                    <div class="mt-3">
                        <div class="d-flex align-items-center">
                            <h3 class="card-subtitle">Promociones</h3>
                        </div>
                        <div v-if="promotions.length>0" class="table-responsive p-1">
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
                        <span class="d-flex fst-italic justify-content-center">No existen promociones diponibles para vincular actualmente</span>
                    </div>
                    <div class="d-flex gap-2 mt-3 justify-content-center">
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
import ServicesService from '@/services/ServicesService';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import DataTable from '@/components/tables/dataTable.vue';
import PromotionsService from '@/services/PromotionService';
import { showSuccess } from '@/utils/sweetAlert';


// ---------------------------------------------------------------------------
// Section 1: view load
// ---------------------------------------------------------------------------
const cargando = ref(false);
const imagePreview = ref(null)
const errors = ref({});
const router = useRouter();
const newServiceForm = ref({
    name: '',
    description: '',
    cost: '',
    price: '',
    service_image: null,
});

onMounted(async () => {
    await getAllPromotions();
})

const newService = async () => {
    try {
        errors.value = {}
        const formData = new FormData()

        formData.append('name', newServiceForm.value.name)
        formData.append('description', newServiceForm.value.description)
        formData.append('cost', newServiceForm.value.cost)
        formData.append('price', newServiceForm.value.price)

        if (newServiceForm.value.service_image) {
            formData.append(
                'service_image',
                newServiceForm.value.service_image
            )
        }

        selectedPromotionIds.value.forEach(id => {
            formData.append('promotions[]', id)
        })

        await ServicesService.createService(formData)

        // limpiar el formulario
        newServiceForm.value = {
            name: '',
            description: '',
            cost: '',
            price: '',
            service_image: null
        }
        errors.value = {}

        imagePreview.value = null
        await showSuccess('Servicio creado correctamente')
        goBack();
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
const handleImageChange = (event) => {
    const file = event.target.files[0]

    if (!file) {
        newServiceForm.value.service_image = null
        imagePreview.value = null
        return
    }

    newServiceForm.value.service_image = file
    imagePreview.value = URL.createObjectURL(file)
}

// ---------------------------------------------------------------------------
// Section 2: dataTable
// ---------------------------------------------------------------------------
const selectedPromotionIds = ref([]);
const promotions = ref([]);
const promotionsColumns = [
    {data: 'id',title: 'Selección',className: 'text-center',width: '80px'},
    {data: 'name',title: 'Nombre',className: 'text-center'},
    {data: 'discount_type',title: 'Tipo',className: 'd-none d-sm-table-cell text-center'},
    {data: 'discount_value',title: 'Valor',className: 'text-center'},
    {data: 'end_date',title: 'Fecha de fin',className: 'd-none d-sm-table-cell text-center'}
]

const goBack = () => {
    router.push({ name: 'services' })
}

const getAllPromotions = async () => {
    try {
        cargando.value = true;
        const response = await PromotionsService.getPromotions();

        promotions.value = response.data.data.filter(
            promotion => promotion.status === 'active'
        );

    } catch (err) {
        console.error('Error al cargar promociones:', err);
        error.value = 'No se pudieron cargar las promociones';
    }
    finally {
        cargando.value = false;
    }
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
</script>