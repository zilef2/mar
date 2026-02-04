<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, DoughnutController } from 'chart.js'
import {zilef_number_format,NumToHour} from '@/global.ts';
import { QuestionMarkCircleIcon, ArrowDownIcon, ArrowUpIcon, ArrowsRightLeftIcon } from '@heroicons/vue/24/solid';
import 'floating-vue/dist/style.css';
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, DoughnutController)
import { createAcquisitionsChart, charSuma, createAcquisitionsChart3, createAcquisitionsChart4,charSuma2 } from './dimensions.js';
import Chart from 'chart.js/auto';

const props = defineProps({
    roles: Number,
    reportes: Number,
    permissions: Number,
    acquisitionsData: { type: Array, required: true },
    acquisitionsData2: { type: Array, required: true },
    acquisitionsData3: { type: Array, required: true },
    acquisitionsData4: { type: Object, required: true },
    acquisitionsData5: { type: Array, required: true },
    year: Number,
    month: Number,
    int_limit: Number,
    porcentajeJornada: Number,
    promedioDiario: Number,
    bloqueoNoPago: Boolean,
    totalOps: Number,
    opsEnProceso: Number,
    opsCompletadas: Number,
    parosHoy: Number,
    totalReprocesosMes: Number,
    causasParo: { type: Array, required: true },
})

const singleColumnView = ref(false);

const canvas1 = ref(null);
const canvas2 = ref(null);
const canvas3 = ref(null);
const canvas4 = ref(null);
const canvas5 = ref(null);
const canvasCausasParo = ref(null);

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
    chart4, chart5, chartCausasParo;

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
    if (canvasCausasParo.value && props.causasParo) {
        if(chartCausasParo) chartCausasParo.destroy();
        const labels = props.causasParo.map(item => item.causa);
        const data = props.causasParo.map(item => item.total);
        chartCausasParo = new Chart(canvasCausasParo.value, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total de Paros',
                    data: data,
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Causas de Paro en el Mes'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});

</script>
<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 lg:p-8 dark:text-gray-200">
            <!-- Controles de Fecha y Vista -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-2">
                    <button @click="goToPreviousMonth" class="px-4 py-2 dark:bg-gray-700 dark:hover:bg-gray-600 bg-white hover:bg-gray-100 text-gray-800 dark:text-white text-sm font-medium rounded-md shadow-sm transition">
                        Mes anterior
                    </button>
                    <span class="text-xl font-semibold">{{ displayedDate }}</span>
                    <button @click="goToNextMonth" :disabled="isCurrentMonth" class="px-4 py-2 dark:bg-gray-700 dark:hover:bg-gray-600 bg-white hover:bg-gray-100 text-gray-800 dark:text-white text-sm font-medium rounded-md shadow-sm transition"
                            :class="{'opacity-50 cursor-not-allowed': isCurrentMonth}">
                        Mes posterior
                    </button>
                </div>
                <button @click="singleColumnView = !singleColumnView" class="px-4 py-2 dark:bg-gray-700 dark:hover:bg-gray-600 bg-white hover:bg-gray-100 text-gray-800 dark:text-white text-sm font-medium rounded-md shadow-sm transition">
                    {{ singleColumnView ? 'Vista Múltiple' : 'Vista Sencilla' }}
                </button>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 p-6 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 ease-in-out text-center border border-green-200 dark:border-green-700/50 group cursor-pointer">
                    <div class="flex justify-center mb-2">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center group-hover:bg-green-600 transition-colors duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Completadas</h3>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-200">{{ props.opsCompletadas }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 p-6 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 ease-in-out text-center border border-blue-200 dark:border-blue-700/50 group cursor-pointer">
                    <div class="flex justify-center mb-2">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">En Proceso</h3>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">{{ props.opsEnProceso }}</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/30 p-6 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 ease-in-out text-center border border-amber-200 dark:border-amber-700/50 group cursor-pointer">
                    <div class="flex justify-center mb-2">
                        <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center group-hover:bg-amber-600 transition-colors duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Órdenes Totales</h3>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors duration-200">{{ props.totalOps }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 p-6 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 ease-in-out text-center border border-red-200 dark:border-red-700/50 group cursor-pointer">
                    <div class="flex justify-center mb-2">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center group-hover:bg-red-600 transition-colors duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Paros Hoy</h3>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-200">{{ props.parosHoy }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 p-6 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 ease-in-out text-center border border-purple-200 dark:border-purple-700/50 group cursor-pointer">
                    <div class="flex justify-center mb-2">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center group-hover:bg-purple-600 transition-colors duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Reprocesos (Mes)</h3>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-200">{{ props.totalReprocesosMes }}</p>
                </div>
            </div>
            
            <div class="grid gap-2" :class="singleColumnView ? 'grid-cols-1' : 'grid-cols-2'">
                
                 <!-- Tiempos por trabajador (Top) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-4">
                    <h2 class="text-center text-lg font-medium">Top {{props.int_limit}} Trabajadores con Más Horas</h2>
                    <canvas ref="canvas2"></canvas>
                </div>

                <!-- Tiempos por trabajador (Bottom) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-4">
                    <h2 class="text-center text-lg font-medium">Top {{props.int_limit}} Trabajadores con Menos Horas</h2>
                    <canvas ref="canvas5"></canvas>
                </div>
                
                
                <!-- Nueva Gráfica: Causas de Paro -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-4">
                    <canvas ref="canvasCausasParo"></canvas>
                </div>

                <!-- Relacion reportes (Dona) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-4 flex items-center justify-center">
                    <div class="w-full max-w-xs">
                        <canvas ref="canvas4"></canvas>
                    </div>
                </div>

               

                <!-- Analisis de Actividades -->
                <div v-if="bloqueoNoPago" class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-4 md:col-span-2">
                    <h2 class="text-xl font-bold text-center mb-4">Análisis de Actividades</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-2">
                            <h3 class="text-lg font-medium text-center mb-2">Promedio Semanal</h3>
                            <canvas ref="canvas1"></canvas>
                        </div>
                        <div class="p-2">
                            <h3 class="text-lg font-medium text-center mb-2">Promedio Mensual</h3>
                            <canvas ref="canvas3"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Indicadores de Eficiencia -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-6 flex flex-col items-center justify-center h-full">
                    <h3 class="text-xl font-bold mb-4">Indicadores de Eficiencia</h3>
                    <div class="flex space-x-8">
                        <div class="text-center">
                            <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Eficiencia Jornada</h4>
                            <p class="text-4xl font-bold mt-2">{{(props.porcentajeJornada)}}<span class="text-3xl">%</span></p>
                        </div>
                        <div class="text-center">
                            <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Horas Promedio Diarias</h4>
                            <p class="text-4xl font-bold mt-2">{{NumToHour(props.promedioDiario)}}</p>
                        </div>
                    </div>
                </div>
                <!-- ? -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg p-6 flex flex-col items-center justify-center h-full">
                    <h3 class="text-xl font-bold mb-4">Indicadores de </h3>
                    <div class="flex space-x-8"> ?
<!--                        <div class="text-center">-->
<!--                            <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Eficiencia Jornada</h4>-->
<!--                            <p class="text-4xl font-bold mt-2">{{(props.porcentajeJornada)}}<span class="text-3xl">%</span></p>-->
<!--                        </div>-->
<!--                        <div class="text-center">-->
<!--                            <h4 class="text-lg font-medium text-gray-500 dark:text-gray-400">Horas Promedio Diarias</h4>-->
<!--                            <p class="text-4xl font-bold mt-2">{{NumToHour(props.promedioDiario)}}</p>-->
<!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
