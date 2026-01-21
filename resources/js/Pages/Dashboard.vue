<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

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
                    <button @click="goToPreviousMonth" class="px-4 py-2 text-white bg-gray-600 hover:bg-gray-700 rounded-md">
                        Mes anterior
                    </button>
                    <span class="text-lg font-semibold">{{ displayedDate }}</span>
                    <button @click="goToNextMonth" :disabled="isCurrentMonth" class="px-4 py-2 text-white bg-gray-600 hover:bg-gray-700 rounded-md" :class="{'opacity-50 cursor-not-allowed': isCurrentMonth}">
                        Mes posterior
                    </button>
                </div>
                <button @click="singleColumnView = !singleColumnView" class="px-4 py-2 text-white bg-gray-600 hover:bg-gray-700 rounded-md">
                    {{ singleColumnView ? 'Vista de 2 Columnas' : 'Vista de 1 Columna' }}
                </button>
            </div>
            <div class="grid gap-1 lg:gap-2" :class="singleColumnView ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2'">
                <!-- Chart 1 -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-1">
                        Tiempos por trabajador (44h/semana o 10560 mins/mes)
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-gray-200 text-center my-1">
                        {{props.int_limit}} primeros trabajadores
                    </p>
                    <canvas ref="canvas2"></canvas>
                </div>
                <!-- Chart 1.5 -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-1">
                        Tiempos por trabajador (menor tiempo)
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-gray-200 text-center my-1">
                        {{props.int_limit}} ultimos trabajadores
                    </p>
                    <canvas ref="canvas5"></canvas>
                </div>

                <!-- Chart 2 -->
                <div v-if="bloqueoNoPago" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                        Actividades (semanal)
                    </h2>
                    <canvas ref="canvas1"></canvas>
                </div>

                <!-- Chart 3 -->
                <div v-if="bloqueoNoPago" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                        Actividades (Mensual)
                    </h2>
                    <canvas ref="canvas3"></canvas>
                </div>

                <!-- Chart 4 -->
                <div v-if="bloqueoNoPago" class="w-80 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                        Relacion reportes
                    </h2>
                    <canvas ref="canvas4"></canvas>
                </div>
                

            </div>
        </div>
    </AuthenticatedLayout>
</template>
