<template>
    <div >

        <DataTable
            :data="data"
            :columns="columns"
            :options="tableOptions"
            class="table table-striped table-hover"
            style="width: 100%"
        >

            <thead>
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.data"
                        class="text-center"
                    >
                        {{ column.title }}
                    </th>
                </tr>
            </thead>

            <template
                v-for="(_, index) in columns"
                :key="index"
                #[`column-${index}`]="props"
            >
                <slot
                    :name="`column-${index}`"
                    v-bind="props"
                />
            </template>

        </DataTable>

    </div>
</template>


<script setup>

import DataTable from 'datatables.net-vue3'
import DataTablesCore from 'datatables.net-bs5'

import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css'


DataTable.use(DataTablesCore)


/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({

    data: {
        type: Array,
        default: () => []
    },

    columns: {
        type: Array,
        required: true
    },

    pageLength: {
        type: Number,
        default: 10
    },
    showAllOption: {
    type: Boolean,
    default: true
}

})


/*
|--------------------------------------------------------------------------
| Configuración DataTables
|--------------------------------------------------------------------------
*/

const tableOptions = {

    pageLength: props.pageLength,


    lengthMenu: props.showAllOption
        ? [
            [10, 25, 50, -1],
            [10, 25, 50, -1]
        ]
        : [
            [10, 25, 50],
            [10, 25, 50]
        ],

    searching: true,

    paging: true,

    ordering: true,

    info: true,

    language: {

        search: 'Buscar:',

        lengthMenu: 'Mostrar _MENU_ registros',

        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',

        infoEmpty: 'No hay registros para mostrar',

        zeroRecords: 'No se encontraron registros',

        emptyTable: 'No hay datos disponibles',

        lengthLabels: {
            '-1': 'Todos'
        },

        paginate: {
            first: 'Primero',
            last: 'Último',
            next: 'Siguiente',
            previous: 'Anterior'
        }

    }

}

</script>
