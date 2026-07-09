<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { reactive } from 'vue';
import { ArrowUpCircleIcon, RectangleStackIcon, UserGroupIcon, DocumentIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    title: String,
    numUsuarios: Number,
})

const page = usePage();

const form = useForm({
    archivo1: null,
});

const form2 = useForm({
    archivo2: null,
});

const form3 = useForm({
    archivo3: null,
});

function uploadFiletrabajadors() {
    form.post(route('uploadUser'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function uploadFileOrdenes() {
    form2.post(route('uploadOP'), {
        preserveScroll: true,
        onSuccess: () => form2.reset(),
    });
}

function uploadFileInterruptores() {
    form3.transform((data) => ({
        archivo1: data.archivo3
    })).post(route('upload.excel', { importClass: 'InterruptoresImport' }), {
        preserveScroll: true,
        onSuccess: () => form3.reset(),
    });
}

const columnsOP = [
    "op", "cliente", "obra", "producto_descripcion", "asesor", "estado", "cantidad"
];

const columnsInterruptores = [
    "Referencia", "Descripción", "Valor", "Costo con descuento", "Valor unitario"
];

const state = reactive({
    showHelp: {
        trabajadores: false,
        ordenes: true,
        interruptores: false
    }
});
</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ props.title }}
                </h2>
                <div class="flex items-center space-x-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                    <span>Formatos admitidos:</span>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-800/60 rounded text-gray-700 dark:text-gray-300 font-mono">.xlsx</span>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-800/60 rounded text-gray-700 dark:text-gray-300 font-mono">.xls</span>
                </div>
            </div>
        </template>

        <div class="py-10 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Feedback Messages -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-4"
                >
                    <div v-if="$page.props.flash.warning || $page.props.flash.success" class="mb-6">
                        <div :class="[
                            'p-4 rounded-xl flex items-start shadow-lg border backdrop-blur-sm',
                            $page.props.flash.warning 
                                ? 'bg-amber-500/10 text-amber-900 dark:text-amber-200 border-amber-500/30' 
                                : 'bg-emerald-500/10 text-emerald-900 dark:text-emerald-200 border-emerald-500/30'
                        ]">
                            <div class="mr-3 mt-0.5">
                                <svg v-if="$page.props.flash.warning" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3.L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <svg v-else class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="font-medium flex-1">
                                {{ $page.props.flash.warning || $page.props.flash.success }}
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Global Import Guidelines Banner -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 border border-amber-200/60 dark:border-amber-900/40 rounded-2xl p-5 shadow-sm flex items-start space-x-4">
                    <div class="p-3 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-800 dark:text-amber-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3.L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-amber-950 dark:text-amber-200">Requisitos Críticos del Archivo Excel</h3>
                        <p class="text-sm text-amber-800/90 dark:text-amber-300/90 mt-1 leading-relaxed">
                            Para evitar fallos o registros omitidos durante la importación, asegúrese de cumplir con las siguientes directrices:
                        </p>
                        <ul class="mt-2 space-y-1.5 text-xs text-amber-800/90 dark:text-amber-300/90 list-disc pl-5">
                            <li><strong class="text-amber-900 dark:text-amber-100">Todas las columnas son obligatorias:</strong> Todas las columnas indicadas en los formatos deben estar presentes en el archivo y contener datos. Las filas con celdas vacías pueden ser omitidas o generar errores.</li>
                            <li><strong class="text-amber-900 dark:text-amber-100">Celda A1 en Órdenes:</strong> El primer encabezado (columna A, fila 1) debe tener el nombre exacto de <code class="px-1.5 py-0.5 bg-amber-200/50 dark:bg-amber-900/60 rounded font-mono text-amber-950 dark:text-amber-100 font-semibold">op</code> en minúsculas.</li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Form 1: Trabajadores -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 dark:border-gray-700/60 transition-all hover:shadow-xl hover:-translate-y-0.5 duration-300 flex flex-col group">
                        <div class="p-6 flex-grow">
                            <!-- Card Header -->
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl mr-4 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition-transform duration-300">
                                    <UserGroupIcon class="h-7 w-7" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Trabajadores</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Carga masiva de personal</p>
                                </div>
                            </div>

                            <!-- Upload Form -->
                            <form @submit.prevent="uploadFiletrabajadors" class="space-y-4 mb-6">
                                <div class="relative">
                                    <!-- Visual Dropzone -->
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer bg-gray-50/50 dark:bg-gray-900/30 hover:bg-gray-50 dark:hover:bg-gray-900/50 hover:border-blue-400 dark:hover:border-blue-500/50 transition-all duration-200">
                                        <div class="flex flex-col items-center justify-center pt-4 pb-4 px-2 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 mb-2 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                            </svg>
                                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                                                <span>Seleccionar archivo Excel</span>
                                            </p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Arrastre o haga clic para buscar</p>
                                        </div>
                                        <input 
                                            type="file" 
                                            @input="form.archivo1 = $event.target.files[0]"
                                            class="hidden" 
                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" 
                                        />
                                    </label>

                                    <!-- Selected File Indicator -->
                                    <div v-if="form.archivo1" class="mt-3 flex items-center justify-between p-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900/40 rounded-lg text-xs">
                                        <div class="flex items-center space-x-2 truncate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-medium text-blue-900 dark:text-blue-200 truncate">{{ form.archivo1.name }}</span>
                                        </div>
                                        <button type="button" @click="form.archivo1 = null" class="text-blue-500 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 rounded p-1 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div v-if="form.progress" class="mt-3 h-1.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 dark:bg-blue-500 transition-all duration-300" :style="{ width: form.progress.percentage + '%' }"></div>
                                    </div>
                                </div>

                                <PrimaryButton :disabled="form.archivo1 == null || form.processing" class="w-full justify-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all text-xs font-semibold">
                                    <ArrowUpCircleIcon class="h-4.5 w-4.5 mr-2" />
                                    Importar Trabajadores
                                </PrimaryButton>
                            </form>

                            <!-- Format Structure Section -->
                            <div class="border-t border-gray-100 dark:border-gray-700/60 pt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Estructura del Excel</span>
                                    <span class="px-2 py-0.5 bg-red-105 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-bold rounded-full">Columnas Obligatorias</span>
                                </div>

                                <!-- Excel Mockup Preview -->
                                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-750">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-[11px] text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-900 font-mono text-[9px] text-gray-400">
                                            <tr>
                                                <th class="px-2 py-1 text-center bg-gray-100 dark:bg-gray-800 border-r border-b border-gray-200 dark:border-gray-700 w-8"></th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">A</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">B</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">C</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">D</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">E</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">F</th>
                                                <th class="px-2 py-1 border-b border-gray-200 dark:border-gray-700 text-center">G</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono text-gray-700 dark:text-gray-300">
                                            <tr class="bg-white dark:bg-gray-800 font-semibold">
                                                <td class="px-2 py-1 text-center bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 text-gray-400 text-[9px]">1</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400">nombre</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400">cc</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400">cargo</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400">salario</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400">nacimiento</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-blue-600 dark:text-blue-400">dir</td>
                                                <td class="px-2 py-1 text-blue-600 dark:text-blue-400">cel</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Accordion Details -->
                                <div class="mt-3">
                                    <button 
                                        type="button" 
                                        @click="state.showHelp.trabajadores = !state.showHelp.trabajadores"
                                        class="flex items-center text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 focus:outline-none"
                                    >
                                        <span>{{ state.showHelp.trabajadores ? 'Ocultar detalles' : 'Ver guía detallada de columnas' }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform" :class="{'rotate-180': state.showHelp.trabajadores}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div v-show="state.showHelp.trabajadores" class="mt-2.5 text-xs text-gray-600 dark:text-gray-400 space-y-1.5 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                                        <p><strong class="text-gray-800 dark:text-gray-200">A1: nombre</strong> - Nombre completo del trabajador.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">B1: cc</strong> - Cédula / Identificación (numérico).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">C1: cargo</strong> - Puesto o rol laboral.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">D1: salario</strong> - Salario del trabajador (numérico).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">E1: nacimiento</strong> - Fecha (Formato AAAA-MM-DD o formato fecha Excel).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">F1: dir</strong> - Dirección de domicilio.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">G1: cel</strong> - Teléfono celular de contacto.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-gray-50 dark:bg-gray-900/40 px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex justify-between items-center text-xs text-gray-500 dark:text-gray-450">
                            <span>Total registros en sistema</span>
                            <span class="font-bold text-gray-900 dark:text-white px-3 py-1 bg-white dark:bg-gray-800 rounded-full shadow-sm border border-gray-100 dark:border-gray-700">{{ props.numUsuarios }}</span>
                        </div>
                    </div>

                    <!-- Form 2: Ordenes de Produccion -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-2xl border-2 border-indigo-500/20 dark:border-indigo-500/30 transition-all hover:shadow-xl hover:-translate-y-0.5 duration-300 flex flex-col group relative">
                        <!-- Top highlighted indicator for op constraint -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-amber-500 to-indigo-600"></div>
                        
                        <div class="p-6 flex-grow">
                            <!-- Card Header -->
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl mr-4 text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform duration-300">
                                    <RectangleStackIcon class="h-7 w-7" />
                                </div>
                                <div>
                                    <div class="flex items-center space-x-1.5">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Órdenes</h3>
                                        <span class="px-1.5 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-[9px] font-black rounded uppercase tracking-wider animate-pulse">A1=op</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Carga de producción</p>
                                </div>
                            </div>

                            <!-- Upload Form -->
                            <form @submit.prevent="uploadFileOrdenes" class="space-y-4 mb-6">
                                <div class="relative">
                                    <!-- Visual Dropzone -->
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer bg-gray-50/50 dark:bg-gray-900/30 hover:bg-gray-50 dark:hover:bg-gray-900/50 hover:border-indigo-400 dark:hover:border-indigo-500/50 transition-all duration-200">
                                        <div class="flex flex-col items-center justify-center pt-4 pb-4 px-2 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 mb-2 group-hover:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                            </svg>
                                            <p class="text-xs font-semibold text-gray-650 dark:text-gray-400">
                                                <span>Seleccionar archivo Excel</span>
                                            </p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Arrastre o haga clic para buscar</p>
                                        </div>
                                        <input 
                                            type="file" 
                                            @input="form2.archivo2 = $event.target.files[0]"
                                            class="hidden" 
                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" 
                                        />
                                    </label>

                                    <!-- Selected File Indicator -->
                                    <div v-if="form2.archivo2" class="mt-3 flex items-center justify-between p-2.5 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-900/40 rounded-lg text-xs">
                                        <div class="flex items-center space-x-2 truncate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-medium text-indigo-900 dark:text-indigo-200 truncate">{{ form2.archivo2.name }}</span>
                                        </div>
                                        <button type="button" @click="form2.archivo2 = null" class="text-indigo-500 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 rounded p-1 transition-colors">
                                            <svg xmlns="http://www.w3.org/2050/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div v-if="form2.progress" class="mt-3 h-1.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-600 dark:bg-indigo-500 transition-all duration-300" :style="{ width: form2.progress.percentage + '%' }"></div>
                                    </div>
                                </div>

                                <PrimaryButton :disabled="form2.archivo2 == null || form2.processing" class="w-full justify-center py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all text-xs font-semibold">
                                    <ArrowUpCircleIcon class="h-4.5 w-4.5 mr-2" />
                                    Importar Órdenes
                                </PrimaryButton>
                            </form>

                            <!-- Format Structure Section -->
                            <div class="border-t border-gray-100 dark:border-gray-700/60 pt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Estructura del Excel</span>
                                    <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-bold rounded-full">Columnas Obligatorias</span>
                                </div>

                                <!-- Excel Mockup Preview -->
                                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-750">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-[11px] text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-900 font-mono text-[9px] text-gray-400">
                                            <tr>
                                                <th class="px-2 py-1 text-center bg-gray-100 dark:bg-gray-800 border-r border-b border-gray-200 dark:border-gray-700 w-8"></th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-400 font-bold ring-1 ring-amber-500/20">A</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">B</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">C</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">D</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">E</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">F</th>
                                                <th class="px-2 py-1 border-b border-gray-200 dark:border-gray-700 text-center">G</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono text-gray-700 dark:text-gray-300">
                                            <tr class="bg-white dark:bg-gray-800 font-semibold">
                                                <td class="px-2 py-1 text-center bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 text-gray-400 text-[9px]">1</td>
                                                <!-- A1 must say op -->
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 bg-amber-500/10 text-amber-850 dark:text-amber-400 ring-2 ring-amber-500 font-black text-center">op</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-indigo-600 dark:text-indigo-400">cliente</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-indigo-600 dark:text-indigo-400">obra</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-indigo-600 dark:text-indigo-400">producto_descripcion</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-indigo-600 dark:text-indigo-400">asesor</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-indigo-600 dark:text-indigo-400">estado</td>
                                                <td class="px-2 py-1 text-indigo-600 dark:text-indigo-400">cantidad</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 flex items-start space-x-1 bg-amber-50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30 p-2 rounded-lg text-[10px] text-amber-800 dark:text-amber-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <span>La celda <strong class="text-amber-950 dark:text-amber-100">A1</strong> debe contener textualmente <strong class="text-amber-950 dark:text-amber-100">op</strong>. Todos los campos de esta fila y filas subsiguientes son requeridos sin excepción.</span>
                                </div>

                                <!-- Accordion Details -->
                                <div class="mt-3">
                                    <button 
                                        type="button" 
                                        @click="state.showHelp.ordenes = !state.showHelp.ordenes"
                                        class="flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 focus:outline-none"
                                    >
                                        <span>{{ state.showHelp.ordenes ? 'Ocultar detalles' : 'Ver guía detallada de columnas' }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform" :class="{'rotate-180': state.showHelp.ordenes}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div v-show="state.showHelp.ordenes" class="mt-2.5 text-xs text-gray-600 dark:text-gray-400 space-y-1.5 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                                        <p><strong class="text-gray-800 dark:text-gray-200">A1: op</strong> - Número de orden de producción (clave única, obligatorio).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">B1: cliente</strong> - Nombre de la empresa o cliente solicitante.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">C1: obra</strong> - Nombre o ubicación del proyecto / obra.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">D1: producto_descripcion</strong> - Detalle del producto.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">E1: asesor</strong> - Nombre del asesor comercial asignado.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">F1: estado</strong> - Estado inicial de la orden.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">G1: cantidad</strong> - Cantidad solicitada (numérico).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-gray-50 dark:bg-gray-900/40 px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex justify-between items-center text-xs text-gray-550 dark:text-gray-400">
                            <span>Formato estructurado con encabezado</span>
                            <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded font-semibold border border-indigo-100 dark:border-indigo-800">Maatwebsite v3</span>
                        </div>
                    </div>

                    <!-- Form 3: Interruptores -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 dark:border-gray-700/60 transition-all hover:shadow-xl hover:-translate-y-0.5 duration-300 flex flex-col group">
                        <div class="p-6 flex-grow">
                            <!-- Card Header -->
                            <div class="flex items-center mb-6">
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl mr-4 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition-transform duration-300">
                                    <DocumentIcon class="h-7 w-7" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Interruptores</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Carga de inventario y catálogo</p>
                                </div>
                            </div>

                            <!-- Upload Form -->
                            <form @submit.prevent="uploadFileInterruptores" class="space-y-4 mb-6">
                                <div class="relative">
                                    <!-- Visual Dropzone -->
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer bg-gray-50/50 dark:bg-gray-900/30 hover:bg-gray-50 dark:hover:bg-gray-900/50 hover:border-emerald-400 dark:hover:border-emerald-500/50 transition-all duration-200">
                                        <div class="flex flex-col items-center justify-center pt-4 pb-4 px-2 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 mb-2 group-hover:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                            </svg>
                                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                                                <span>Seleccionar archivo Excel</span>
                                            </p>
                                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Arrastre o haga clic para buscar</p>
                                        </div>
                                        <input 
                                            type="file" 
                                            @input="form3.archivo3 = $event.target.files[0]"
                                            class="hidden" 
                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" 
                                        />
                                    </label>

                                    <!-- Selected File Indicator -->
                                    <div v-if="form3.archivo3" class="mt-3 flex items-center justify-between p-2.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/40 rounded-lg text-xs">
                                        <div class="flex items-center space-x-2 truncate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-medium text-emerald-900 dark:text-emerald-200 truncate">{{ form3.archivo3.name }}</span>
                                        </div>
                                        <button type="button" @click="form3.archivo3 = null" class="text-emerald-500 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 rounded p-1 transition-colors">
                                            <svg xmlns="http://www.w3.org/2050/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div v-if="form3.progress" class="mt-3 h-1.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-600 dark:bg-emerald-500 transition-all duration-300" :style="{ width: form3.progress.percentage + '%' }"></div>
                                    </div>
                                </div>

                                <PrimaryButton :disabled="form3.archivo3 == null || form3.processing" class="w-full justify-center py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all text-xs font-semibold">
                                    <ArrowUpCircleIcon class="h-4.5 w-4.5 mr-2" />
                                    Importar Interruptores
                                </PrimaryButton>
                            </form>

                            <!-- Format Structure Section -->
                            <div class="border-t border-gray-100 dark:border-gray-700/60 pt-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Estructura del Excel</span>
                                    <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-[10px] font-bold rounded-full">Columnas Obligatorias</span>
                                </div>

                                <!-- Excel Mockup Preview -->
                                <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-750">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-[11px] text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-900 font-mono text-[9px] text-gray-400">
                                            <tr>
                                                <th class="px-2 py-1 text-center bg-gray-100 dark:bg-gray-800 border-r border-b border-gray-200 dark:border-gray-700 w-8"></th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">A</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">B</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">C</th>
                                                <th class="px-2 py-1 border-r border-b border-gray-200 dark:border-gray-700 text-center">D</th>
                                                <th class="px-2 py-1 border-b border-gray-200 dark:border-gray-700 text-center">E</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono text-gray-700 dark:text-gray-300">
                                            <tr class="bg-white dark:bg-gray-800 font-semibold">
                                                <td class="px-2 py-1 text-center bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 text-gray-400 text-[9px]">1</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-emerald-600 dark:text-emerald-400">Referencia</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-emerald-600 dark:text-emerald-400">Descripción</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-emerald-600 dark:text-emerald-400">Valor</td>
                                                <td class="px-2 py-1 border-r border-gray-200 dark:border-gray-700 text-emerald-600 dark:text-emerald-400">Costo con descuento</td>
                                                <td class="px-2 py-1 text-emerald-600 dark:text-emerald-400">Valor unitario</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Accordion Details -->
                                <div class="mt-3">
                                    <button 
                                        type="button" 
                                        @click="state.showHelp.interruptores = !state.showHelp.interruptores"
                                        class="flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 focus:outline-none"
                                    >
                                        <span>{{ state.showHelp.interruptores ? 'Ocultar detalles' : 'Ver guía detallada de columnas' }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-1 transform transition-transform" :class="{'rotate-180': state.showHelp.interruptores}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div v-show="state.showHelp.interruptores" class="mt-2.5 text-xs text-gray-600 dark:text-gray-400 space-y-1.5 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                                        <p><strong class="text-gray-800 dark:text-gray-200">A1: Referencia</strong> - Código o SKU del interruptor (clave única).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">B1: Descripción</strong> - Descripción detallada del componente.</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">C1: Valor</strong> - Precio bruto de lista (numérico).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">D1: Costo con descuento</strong> - Precio con descuento aplicado (numérico).</p>
                                        <p><strong class="text-gray-800 dark:text-gray-200">E1: Valor unitario</strong> - Precio unitario final del artículo (numérico).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
