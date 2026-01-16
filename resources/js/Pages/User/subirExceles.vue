<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { reactive, watch } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import pkg from 'lodash';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

import { BookOpenIcon, ArrowUpCircleIcon, ArrowDownCircleIcon } from '@heroicons/vue/24/solid';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { PrimerasPalabras, vectorSelect, formatDate, CalcularEdad, CalcularSexo} from '@/global.ts';


const { _, debounce, pickBy } = pkg
const props = defineProps({
    title: String,
    // fromController: Object,
    breadcrumbs: Object,
    numUsuarios: Number,
})
const data = reactive({
})


const form = useForm({
    archivo1: null,
    // fecha_fin: '2023-04-03T'+horas[1]+':00', //toerase
});
const form2 = useForm({
    archivo2: null,
});

function uploadFiletrabajadors() {
    form.post(route('uploadUser'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => null,
        onFinish: () => null,
    });
}
function uploadFileOrdenes() {
    form2.post(route('uploadOP'), {
        preserveScroll: true,
        onSuccess: () => {
            // emit("close")
            // form.reset()
            // data.respuesta = $page.props.flash.success
        },
        onError: () => null,
        onFinish: () => null,
    });
}

// const downloadExcel = () => { window.open('users/export/' + form.quincena + '/' + (form.fecha_ini.month) + '/' + form.fecha_ini.year, '_blank') }

	
const nombres = //GenericImport.php --private array $camposMapeados = [ 
    {
		//como esta en excel  :  como esta en el model
//		"pedido"               : 'pedido',
		"op"                   : 'op',
		"cliente"              : 'cliente',
		"obra "                : 'obra',
//		"contrato"             : 'contrato',
		"producto_descripcion" : 'producto_descripcion',
		"asesor"               : 'asesor',
		"estado"               : 'estado',
		"cantidad"               : 'cant',
		//		fecha	

}
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-4">

            <div v-if="$page.props.flash.warning" class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <div class="flex max-w-screen-xl shadow-lg rounded-lg">
                        <div class="bg-yellow-600 py-4 px-6 rounded-l-lg flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="fill-current text-white"
                                width="20" height="20">
                                <path fill-rule="evenodd"
                                    d="M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z">
                                </path>
                            </svg>
                        </div>
                        <div
                            class="px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200">
                            <div>
                                {{ $page.props.flash.warning ? $page.props.flash.warning : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-24 mx-auto flex flex-warp">
                        <div v-if="can(['create user'])" class="flex flex-wrap -m-4">
                            <!-- user trabajadors -->
                            <div class="p-4 md:w-1/2">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <ArrowUpCircleIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Subir trabajadores</h3>
                                        <p class="leading-relaxed mb-3"> Este formulario solo permite cargar trabajadores</p>

                                        <form @submit.prevent="uploadFiletrabajadors" id="upload">
                                            <input type="file" @input="form.archivo1 = $event.target.files[0]"
                                                accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                                            <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                                                class="bg-sky-200">
                                                {{ form.progress.percentage }}%
                                            </progress>

                                            <PrimaryButton v-show="can(['create user'])" :disabled="form.archivo1 == null"
                                                class="rounded-none my-4" :class="{ 'bg-gray-200' : form.archivo1 == null}">
                                                {{ lang().button.subir }}
                                            </PrimaryButton>
                                        </form>

                                        <h2 class="text-xl text-gray-900 dark:text-white">El formato necesita las siguientes columnas, el simbolo * significa que es un campo obligatorio</h2>
                                        <ul class="list-decimal my-6 mx-5">
                                            <li class="text-lg">Nombre*</li>
                                            <li class="text-lg">Identificacion* <small class="text-lg">Debe ser un numero (sin puntos ni comas)</small></li>
                                            <li class="text-lg">Area*</li>
                                            <li class="text-lg">Cargo*</li>
                                            <li class="text-lg">Celular</li>
                                            <li class="text-lg">Sexo:<small class="text-lg">Femenino, masculino u otro</small></li>
                                            <li class="text-lg">Fecha de nacimiento</li>
                                        </ul>

                                        <div class="flex items-center flex-wrap my-6">
                                            <a class="text-gray-500 inline-flex items-center md:mb-2 lg:mb-0">Numero de Usuarios: </a>
                                            <span
                                                class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                <svg class="w-1 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                {{ props.numUsuarios }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="can(['create user'])" class="flex flex-wrap -m-4">
                            <!-- user trabajadors -->
                            <div class="p-4 md:w-1/2">
                                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                                    <ArrowUpCircleIcon class=" h-24 lg:h-48 md:h-36 w-full object-cover object-center" />

                                    <div class="p-6">
                                        <h3 class="title-font text-lg font-medium text-gray-900 mb-3">Subir ordenes de produccion</h3>
                                        <p class="leading-relaxed mb-3"> Este formulario solo permite cargar ordenes de produccion</p>

                                        <form @submit.prevent="uploadFileOrdenes" id="upload">
                                            <input type="file" @input="form2.archivo2 = $event.target.files[0]"
                                                accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                                            <progress v-if="form2.progress" :value="form2.progress.percentage" max="100"
                                                class="bg-sky-200">
                                                {{ form2.progress.percentage }}%
                                            </progress>

                                            <PrimaryButton v-show="can(['create user'])" :disabled="form2.archivo2 == null"
                                                class="rounded-none my-4" :class="{ 'bg-gray-200' : form2.archivo2 == null}">
                                                {{ lang().button.subir }}
                                            </PrimaryButton>
                                        </form>

                                        <h2 class="text-xl text-gray-900 dark:text-white">El formato necesita las
                                            siguientes columnas, el simbolo * significa que es un campo obligatorio</h2>
                                        <ul  class="list-decimal my-6 mx-5">

                                            <li v-for="nombre in nombres" class="text-lg">{{ nombre }}</li>
                                        </ul>

                                        <div class="flex items-center flex-wrap my-6">
                                            <a class="text-gray-500 inline-flex items-center md:mb-2 lg:mb-0">Numero de Usuarios: </a>
                                            <span
                                                class="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                                                <svg class="w-1 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                {{ props.numUsuarios }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
<!--                        fin zona-->
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
