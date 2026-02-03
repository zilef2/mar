<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import {reactive, watch, ref, watchEffect, onMounted} from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import {router, usePage, Link, useForm} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import {
    ChevronUpDownIcon,
    CheckCircleIcon,
    PencilIcon,
    TrashIcon,
    ArrowTrendingDownIcon,
    ArrowLongUpIcon
} from '@heroicons/vue/24/solid';
import Create from '@/Pages/reporte/Create.vue';
import TerminarReporte from '@/Pages/reporte/TerminarReporte.vue';
import DeleteBulk from '@/Pages/reporte/DeleteBulk.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InfoButton from '@/Components/InfoButton.vue';
import ClockWorking from '@/Components/uiverse/ClockWorking.vue';
import {TimeTo12Format, formatDate, CalcularAvg, zilef_number_format} from '@/global.ts';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import '@vuepic/vue-datepicker/dist/main.css';

/*
import CongeladoSection from "@/Pages/reporte/CongeladoSection.vue";
*/
const page = usePage()

const {_, debounce, pickBy} = pkg
const props = defineProps({
    fromController: Object,
    total: Number,
    filters: Object,
    breadcrumbs: Object,
    perPage: Number,

    title: String,

    numberPermissions: Number,
    losSelect: Object,
    empleados: Object,
    vectorTriple: {
        type: Array,
        required: true
    }
})

const data = reactive({
    params: {
        search: props.filters.searchDate,
        search2: props.filters.search2,
        search3: props.filters.search3, //tipo reporte 
        search4: props.filters.search4, //OT
        search5: props.filters.search5, //actividad
        search6: props.filters.search6, //mes
        search7: props.filters.search7, //anio
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
        searchdia: props.filters.searchdia,
        soloTiEstimado: props.filters.soloTiEstimado,
        ultimosmeses: props.filters.ultimosmeses,
    },
    generico: null,
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    TerminarOpen: false,
    deleteOpen: false,
    dataSet: usePage().props.app.perpage,
    totalEmpleados: 0,

    deleteBulkOpen: false,
    //v-sticky
    hayCongelado: 0,
})
// text // number // dinero // date // datetime // foreign // decimal
let titulos = [
    {order: 'fecha', label: 'fecha', type: 'date', CanOrder: true},
    {order: 'userino', label: 'trabajador', type: 'foreign', nameid: 'userino', CanOrder: true},
    {order: 'hora_inicial', label: 'hora inicial', type: 'time', CanOrder: true},
    {order: 'hora_final', label: 'hora final', type: 'time', CanOrder: true},
    {
        order: 'tiempo_transcurrido',
        label: 'Tiempo Transcurrido',
        type: 'decimal',
        nameid: 'tiempo_transcurrido',
        CanOrder: true
    },
    {order: 'actividad_id', label: 'actividad', type: 'foreign', nameid: 'actividadsini', CanOrder: true},
    {order: 'Orden', label: 'Orden', type: 'text', CanOrder: false},
    {order: 'MinutosEstimados', label: 'MinutosEstimados', type: 'text', CanOrder: true},
    {order: 'paro_id', label: 'paro', type: 'foreign', nameid: 'paro_s', CanOrder: true},
    {order: 'reproceso_id', label: 'reproceso', type: 'foreign', nameid: 'reproceso_s', CanOrder: true},
];
onMounted(() => {
    if (props.numberPermissions < 2) {
        titulos = titulos.filter(t => t.order !== 'MinutosEstimados');

    }
    const anioActual = new Date().getFullYear();
    data.params.search7 = props.filters.search7 ? parseInt(props.filters.search7) : anioActual
})
const order = (field, CanOrder) => {
    if (CanOrder) {
        data.params.field = field
        data.params.order = data.params.order === "asc" ? "desc" : "asc"
    }
}

watch(() => _.cloneDeep(data.params), debounce(() => {
    let params = pickBy(data.params)
    router.get(route("reporte.index"), params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}, 350))

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = []
    } else {
        props.fromController?.data.forEach((reporte) => {
            data.selectedId.push(reporte.id)
        })
    }
}
const select = () => {
    data.multipleSelect = props.reportes?.data.length === data.selectedId.length;
}
// const form = useForm({ })
watchEffect(() => {
})


const tipoReporte = [
    {value: 'soloreporte', title: 'Reportes'},
    {value: 'soloparo', title: 'Paros'},
    {value: 'soloreproceso', title: 'Reprocesos'},
]
const mostrarTiempoTranscurrido = (raw) => {
    const valor = parseFloat(raw)
    if (isNaN(valor)) return '-'
    if (valor < 1) {
        return zilef_number_format(valor * 60, 0, false) + ' mins'
    } else {
        // let minutossobrantes = valor%60 < 10 ? '0'+valor%60 : valor%60 
        return zilef_number_format(valor, 2 , false) + ' hrs'
    }
}
watchEffect(() => {
    if (page.props.inertiaError === 'expired') {
        window.location.href = route('login')
    }
})

</script>

<template>
    <AuthenticatedLayout>
        <!--        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />-->
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit my-2">

                    <Create v-if="can(['create Reporte'])" :numberPermissions="props.numberPermissions"
                            :show="data.createOpen" @close="data.createOpen = false" :title="props.title"
                            :losSelect=props.losSelect
                            :empleados=props.empleados
                    />

                    <TerminarReporte v-if="can(['read Reporte'])" :numberPermissions="props.numberPermissions"
                                     :show="data.TerminarOpen"
                                     @close="data.TerminarOpen = false" :generica="data.generico" :title="props.title"
                    />

                    <DeleteBulk :show="data.deleteBulkOpen"
                                @close="data.deleteBulkOpen = false, data.multipleSelect = false, data.selectedId = []"
                                :selectedId="data.selectedId" :title="props.title"/>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg ">
                <div
                    class="flex flex-wrap justify-between items-center gap-3 px-2 py-2 bg-white dark:bg-gray-800 shadow-sm rounded-md">

                    <!-- Controles de la izquierda -->
                    <div class="flex flex-wrap items-center gap-1">
                        <DangerButton
                            @click="data.deleteBulkOpen = true"
                            v-show="data.selectedId.length !== 0 && can(['delete Reporte'])"
                            class="px-1 py-2 h-8"
                            v-tooltip="lang().tooltip.delete_selected">
                            <TrashIcon class="w-5 h-5"/>
                        </DangerButton>

                        <PrimaryButton
                            class="rounded-lg px-1 h-9 mt-1 flex items-center justify-center hover:bg-indigo-800"
                            @click="data.createOpen = true"
                            v-if="can(['create Reporte'])">
                            Reportar
                        </PrimaryButton>

                        <!-- filtros -->
                        <div class="flex flex-wrap gap-5 mb-3 ml-4 items-end">

                            <div v-if="numberPermissions > 1" class="w-52">
                                <label class="block text-sm dark:text-white">Trabajador</label>
                                <vSelect :options="props.empleados" label="title"
                                         class="dark:bg-gray-400"
                                         append-to-body
                                         v-model="data.params.search2"></vSelect>
                            </div>

                            <div v-if="numberPermissions > 1" class="w-36">
                                <label class="block text-sm dark:text-white">Tipo</label>
                                <vSelect :options="tipoReporte" label="title"
                                         class="dark:bg-gray-400"
                                         append-to-body
                                         v-model="data.params.search3"></vSelect>
                            </div>

                            <div v-if="numberPermissions > 1" class="w-36">
                                <input type="text" v-model="data.params.search4" placeholder="OP"
                                       class="w-full h-9 rounded-md border-[1px] px-2 border-gray-400 dark:bg-white"/>
                            </div>
                            <div v-if="numberPermissions > 1" class="w-36">
                                <input type="text" v-model="data.params.search5" placeholder="Actividad"
                                       class="w-full h-9 rounded-md border-[1px] px-2 border-gray-400 dark:bg-white"/>
                            </div>
                            <div v-if="numberPermissions > 1" class="w-36">
                                <input type="number" v-model="data.params.search6" placeholder="Mes"
                                       class="w-full h-9 rounded-md border-[1px] px-2 border-gray-400 dark:bg-white"/>
                            </div>
                            <div v-if="numberPermissions > 1" class="w-36">
                                <input type="number" v-model="data.params.search7" placeholder="Año"
                                       class="w-full h-9 rounded-md border-[1px] px-2 border-gray-400 dark:bg-white"/>
                            </div>
                            <div v-if="numberPermissions > 1" class="w-36">
                                <TextInput v-model="data.params.searchDate" type="date"
                                           class="w-36 rounded-lg h-10"
                                           placeholder="Buscar por Fecha (mes o año)"/>
                            </div>
                        </div>
                    </div>

                    <!-- Controles de la derecha -->
                    <div v-if="numberPermissions > 1" class="flex items-center gap-0"></div>
                </div>

                <div
                    class="
                        overflow-y-auto
                        max-h-[60vh]          <!-- móviles -->
                        md:max-h-[70vh]       <!-- pantallas medianas -->
                        lg:max-h-[80vh]       <!-- pantallas grandes -->
                        xl:max-h-[85vh]       <!-- monitores grandes -->
                      "
                >
                    <table v-if="props.total > 0" class=" w-full z-20">
                        <thead
                            class="-mt-1 top-0 bg-gray-200 sticky z-[5] capitalize text-sm dark:border-gray-700 dark:bg-black">
                        <tr class="dark:bg-gray-900/50 text-left">
                            <th v-if="props.numberPermissions > 1" class="px-2 py-4 text-center dark:bg-black">
                                <Checkbox v-model:checked="data.multipleSelect" @change="selectAll"/>
                            </th>
                            <th class="px-2 py-4">Accion</th>
                            <th class="px-2 py-4 text-center">#</th>
                            <th v-on:click="order('fecha', true)" class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">
                                    <span>Fecha</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-on:click="order('userino', true)" class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">
                                    <span>Persona</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-on:click="order('hora_inicial', true)" class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">
                                    <span>Hora inicial</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-on:click="order('hora_final', true)" class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">
                                    <span>Hora final</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-on:click="order('tiempo_transcurrido', true)"
                                class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">

                                    <span>Tiempo Transcurrido</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-on:click="order('actividad_id', true)" class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">

                                    <span>Actividad</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-on:click="order('Orden', true)" class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">

                                    <span>Orden de Producción</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                            <th v-if="props.numberPermissions > 1" v-on:click="order('MinutosEstimados', true)"
                                class="px-2 py-4 cursor-pointer min-w-min">
                                <div class="flex">

                                    <span>Tiempo Estimado</span>
                                    <ChevronUpDownIcon class="w-4 h-4"/>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <!--                        <CongeladoSection-->
                        <!--                            :data="data"-->
                        <!--                            :titulos="titulos"-->
                        <!--                            :fromController="props.fromController"-->
                        <!--                            :can="can"-->
                        <!--                            :lang="lang"-->
                        <!--                            :number_format="zilef_number_format"-->
                        <!--                            :formatDate="formatDate"-->
                        <!--                            :TimeTo12Format="TimeTo12Format"-->
                        <!--                        />-->


                        <tbody>
                        <tr v-for="(clasegenerica, indexu) in props.fromController.data" :key="indexu"
                            class="hover:bg-gray-300 border-b-[1px] border-gray-200">
                            <td v-if="props.numberPermissions > 1"
                                class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">
                                <input
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-blue-600 dark:checked:border-primary"
                                    type="checkbox" @change="select" :value="clasegenerica.id"
                                    v-model="data.selectedId"/>
                            </td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3">
                                <div class="flex justify-center items-center">
                                    <div class="rounded-md overflow-hidden">
                                        <InfoButton v-show="can(['update Reporte']) && clasegenerica.hora_final"
                                                    type="button"
                                                    @click="(data.editOpen = true), (data.generico = clasegenerica)"
                                                    class="px-2 py-1.5 rounded-none" v-tooltip="lang().tooltip.edit">
                                            <PencilIcon class="w-4 h-4"/>
                                        </InfoButton>
                                        <InfoButton v-if="!clasegenerica.hora_final" v-show="can(['create Reporte'])"
                                                    type="button"
                                                    @click="(data.TerminarOpen = true), (data.generico = clasegenerica)"
                                                    class="px-2 py-1.5 rounded-none"
                                                    v-tooltip="lang().tooltip.finalizarTarea">
                                            <CheckCircleIcon class="w-4 h-4"/>
                                        </InfoButton>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center">{{ ++indexu }}</td>
                            <td class="text-xs">{{ clasegenerica['fecha'] }}</td>
                            <td class="text-sm">{{ clasegenerica['userino'] }}</td>
                            <td>{{ TimeTo12Format(clasegenerica['hora_inicial']) }}</td>

                            <td v-if="clasegenerica['hora_final']">
                                {{ TimeTo12Format(clasegenerica['hora_final']) }}
                            </td>
                            <td v-else>
                                <ClockWorking class=""/>
                            </td>

                            <td>{{ mostrarTiempoTranscurrido(clasegenerica['tiempo_transcurrido']) }}</td>
                            <td class="border-b-[1px] border-gray-300">
                                {{ (clasegenerica['actividadsini']) }}
                                <span v-if="clasegenerica['parou']" class="font-bold">
                                    <br>Paro: {{ (clasegenerica['parou']) }} 
                                </span>
                                <span v-if="clasegenerica['reprocesou']" class="font-bold">
                                    <br>Re: {{ (clasegenerica['reprocesou']) }} 
                                </span>
                            </td>
                            <td>{{ (clasegenerica['Orden']) }}</td>
                            <td v-if="props.numberPermissions > 1">{{ zilef_number_format(clasegenerica['MinutosEstimados'],2) }} hrs</td>
                        </tr>

                        <!-- totales -->
                        <tr v-if="false">
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"></td>
                            <td class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"></td>
                            <td v-if="numberPermissions > 1"
                                class="whitespace-nowrap py-4 w-12 px-2 sm:py-3 text-center"></td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"></td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"></td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"></td>
                            <td class="whitespace-nowrap py-4 -px-2 sm:py-3 font-bold text-right">Transcurrido~</td>
                            <td class="whitespace-nowrap py-4 -px-2 text-left">
                                {{ CalcularAvg(props.fromController.data, 'tiempo_transcurrido') }}
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"></td>

                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 font-extrabold text-right">
                                <p v-if="props.numberPermissions > 1" class="">Tiempo Estimado Promedio:</p>
                            </td>
                            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-left">
                                <p v-if="props.numberPermissions > 1" class="">
                                    {{ CalcularAvg(props.fromController.data, 'MinutosEstimados') }}
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <h2 v-else class="text-center text-xl my-8">Sin Registros</h2>
                </div>
                <div v-if="props.total > 0"
                     class="flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700">
                    <SelectInput v-model="data.params.perPage" :dataSet="data.dataSet" class="h-10"/>
                    <Pagination :links="props.fromController" :filters="data.params"/>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
