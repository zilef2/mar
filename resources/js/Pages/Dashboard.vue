<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import {zilef_number_format,NumToHour} from '@/global.ts';
import { QuestionMarkCircleIcon, ArrowDownIcon, ArrowUpIcon, ArrowsRightLeftIcon } from '@heroicons/vue/24/solid';
import 'floating-vue/dist/style.css';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

import { createAcquisitionsChart, charSuma, createAcquisitionsChart3, createAcquisitionsChart4,charSuma2 } from './dimensions.js';

const props = defineProps({
    roles: Number,
    reportes: Number,
    permissions: Number,
    acquisitionsData: {
        type: Array,
        required: true
    },
    acquisitionsData2: { type: Array, required: true },
    acquisitionsData3: { type: Array, required: true },
    acquisitionsData4: { type: Array, required: true },
    acquisitionsData5: { type: Array, required: true },
    acquisitionsData6: { type: Array, required: true },
    year: Number,
    month: Number,
    int_limit: Number,
    porcentajeJornada: Number,
    promedioDiario: Number,
    bloqueoNoPago: Boolean,
})

const singleColumnView = ref(false);

const canvas1 = ref(null);
const canvas2 = ref(null);
const canvas3 = ref(null);
const canvas4 = ref(null);
const canvas5 = ref(null);
const canvas6 = ref(null);

const currentYear = computed(() => Number(props.year));
const currentMonth = computed(() => Number(props.month));

const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

const displayedDate = computed(() => {
    return `${monthNames[currentMonth.value - 1]} ${currentYear.value}`;
});

const isCurrentMonth = computed(() => {
    const today = new Date();
    return currentYear.value === today.getFullYear() && currentMonth.value === today.getMonth() + 1;
});

function goToPreviousMonth() {
    let newMonth = currentMonth.value - 1;
    let newYear = currentYear.value;
    if (newMonth === 0) {
        newMonth = 12;
        newYear--;
    }
    window.location.href = route('dashboard', { year: newYear, month: newMonth });
}

function goToNextMonth() {
    if (isCurrentMonth.value) return;

    let newMonth = currentMonth.value + 1;
    let newYear = currentYear.value;
    if (newMonth === 13) {
        newMonth = 1;
        newYear++;
    }
    window.location.href = route('dashboard', { year: newYear, month: newMonth });
}

let chart1, chart2, chart3,
    chart4, chart5, chart6;

onMounted(() => {
    if (canvas1.value && props.acquisitionsData) {
        if(chart1) chart1.destroy();
        chart1 = createAcquisitionsChart(canvas1.value, props.acquisitionsData);
    }
    if (canvas2.value && props.acquisitionsData2) {
        if(chart2) chart2.destroy();
        chart2 = charSuma(canvas2.value, props.acquisitionsData2);
    }
    if (canvas3.value && props.acquisitionsData3) {
        if(chart3) chart3.destroy();
        chart3 = createAcquisitionsChart3(canvas3.value, props.acquisitionsData3);
    }
    if (canvas4.value && props.acquisitionsData4) {
        if(chart4) chart4.destroy();
        chart4 = createAcquisitionsChart4(canvas4.value, props.acquisitionsData4);
    }
    if (canvas5.value && props.acquisitionsData5) {
        if(chart5) chart5.destroy();
        chart5 = charSuma2(canvas5.value, props.acquisitionsData5);
    }
});

</script>
<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <div class="p-4 sm:p-1 lg:p-8">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-2">
                    <button @click="goToPreviousMonth" class="px-3 py-1 dark:text-white text-xs bg-gray-200 hover:bg-gray-700 rounded-sm transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                        Mes anterior
                    </button>
                    <span class="text-lg font-semibold">{{ displayedDate }}</span>
                    <button @click="goToNextMonth" :disabled="isCurrentMonth" class="px-3 py-1 
                    dark:text-white text-xs bg-gray-600 hover:bg-gray-700 rounded-sm transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                            :class="{'opacity-50 cursor-not-allowed': isCurrentMonth}">
                        Mes posterior
                    </button>
                </div>
                <button @click="singleColumnView = !singleColumnView" class="px-3 py-1 
                text-white text-xs bg-gray-600 hover:bg-gray-700 rounded-sm transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                    {{ singleColumnView ? 'Vista de 2 Columnas' : 'Vista de 1 Columna' }}
                </button>
            </div>
            
            <div class="grid px-4 gap-6" :class="singleColumnView ? 'grid-cols-1 mx-24' : 'grid-cols-1 md:grid-cols-2'">
                <!-- Chart 1 -->
                <div class="bg-gradient-to-br from-sky-100 to-indigo-200 dark:from-purple-700 dark:to-indigo-800 dark:text-white overflow-hidden shadow-lg sm:rounded-lg p-4">
                    <div class="flex justify-center items-center gap-2">
                        <h2 class="text-center">
                            Tiempos por trabajador (44h/semana o 10560 mins/mes)
                        </h2>
                        <button v-tooltip="'Esta gráfica muestra los tiempos de los trabajadores con más horas reportadas en el mes.'" class="text-gray-400 hover:text-gray-200">
                            <QuestionMarkCircleIcon class="h-5 w-5"/>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 text-center my-1">
                        {{props.int_limit}} primeros trabajadores
                    </p>
                    <canvas ref="canvas2"></canvas>
                </div>
                <!-- Chart 1.5 -->
                <div class="bg-gradient-to-br from-sky-100 to-indigo-200 dark:from-purple-700 dark:to-indigo-800 dark:text-white overflow-hidden shadow-lg sm:rounded-lg p-4">
                    <div class="flex justify-center items-center gap-2">
                        <h2 class="text-center">
                            Tiempos por trabajador (menor tiempo)
                        </h2>
                        <button v-tooltip="'Esta gráfica muestra los tiempos de los trabajadores con menos horas reportadas en el mes.'" class="text-gray-400 hover:text-gray-200">
                            <QuestionMarkCircleIcon class="h-5 w-5"/>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 text-center my-1">
                        {{props.int_limit}} ultimos trabajadores
                    </p>
                    <canvas ref="canvas5"></canvas>
                </div>

                <!-- Analisis de Actividades -->
                <div v-if="bloqueoNoPago" class="bg-gradient-to-br from-sky-100 to-indigo-200 dark:from-purple-700 dark:to-indigo-800 dark:text-white overflow-hidden shadow-lg sm:rounded-lg p-4">
                    <div class="flex justify-center items-center gap-2">
                        <h2 class="text-xl font-bold text-center">
                            Análisis de Actividades: Semanal y Mensual
                        </h2>
                        <button v-tooltip="'Desglose de las actividades por semana y por mes.'" class="text-gray-400 hover:text-gray-200">
                            <QuestionMarkCircleIcon class="h-5 w-5"/>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Chart 2 -->
                        <div class="bg-white/10 dark:bg-black/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-2">
                            <h3 class="text-lg font-medium text-center mb-2">
                                Semanal
                            </h3>
                            <canvas ref="canvas1"></canvas>
                        </div>

                        <!-- Chart 3 -->
                        <div class="bg-white/10 dark:bg-black/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-2">
                            <h3 class="text-lg font-medium text-center mb-2">
                                Mensual
                            </h3>
                            <canvas ref="canvas3"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart 4 y Metricas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Chart 4 -->
                    <div class="bg-gradient-to-br from-sky-100 to-indigo-200 dark:from-purple-700 dark:to-indigo-800 dark:text-white w-full overflow-hidden shadow-lg sm:rounded-lg p-4">
                        <div class="flex justify-center items-center gap-2">
                            <h2 class="text-lg font-medium text-center">
                                Relacion reportes
                            </h2>
                            <button v-tooltip="'Esta gráfica muestra la relación entre diferentes tipos de reportes.'" class="text-gray-400 hover:text-gray-200">
                                <QuestionMarkCircleIcon class="h-5 w-5"/>
                            </button>
                        </div>
                        <canvas ref="canvas4"></canvas>
                    </div>
<!--                    chart ?-->
                    <div class="bg-gradient-to-br from-sky-100 to-indigo-200 dark:from-purple-700 dark:to-indigo-800 dark:text-white w-full overflow-hidden shadow-lg sm:rounded-lg p-4">
                        <div class="flex justify-center items-center gap-2">
                            <h2 class="text-lg font-medium text-center">
                                
                            </h2>
                            <button v-tooltip="'Esta gráfica muestra ?.'" class="text-gray-400 hover:text-gray-200">
                                <QuestionMarkCircleIcon class="h-5 w-5"/>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="">

                    <!-- Metricas Combinadas -->
                    <div class="bg-gradient-to-br from-sky-100 to-indigo-200 dark:from-purple-700 dark:to-indigo-800 overflow-hidden shadow-lg sm:rounded-lg p-6 flex flex-col items-center justify-center h-full dark:text-white">
                        <div class="flex justify-center items-center gap-2">
                            <h3 class="text-xl font-bold">Indicadores</h3>
                            <button v-tooltip="'Estos son  el promedio y la eficiencia.'" class="text-gray-400 hover:text-gray-200">
                                <QuestionMarkCircleIcon class="h-5 w-5"/>
                            </button>
                        </div>
                        <div class="flex space-x-8 mt-4">
                            <div class="text-center">
                                <h4 class="text-lg font-medium">Eficiencia (8 h / diarias)</h4>
                                <p class="text-4xl font-bold mt-2">{{(props.porcentajeJornada)}}<span class="text-3xl">%</span></p>
                            </div>
                            <div class="text-center">
                                <h4 class="text-lg font-medium">Horas Promedio diarias</h4>
                                <p class="text-4xl font-bold mt-2">{{NumToHour(props.promedioDiario)}}<span class="text-3xl"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
