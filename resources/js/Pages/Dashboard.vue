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
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Órdenes Totales</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ props.totalOps }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">En Proceso</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ props.opsEnProceso }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Completadas</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ props.opsCompletadas }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Paros Hoy</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ props.parosHoy }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Reprocesos (Mes)</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ props.totalReprocesosMes }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-6" :class="singleColumnView ? 'md:grid-cols-1' : 'md:grid-cols-2'">
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
