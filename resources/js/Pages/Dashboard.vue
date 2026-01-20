<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {watchEffect, ref, onMounted} from 'vue';
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

import {createAcquisitionsChart,charSuma,createAcquisitionsChart3,createAcquisitionsChart4} from './dimensions.js';
const props = defineProps({
    roles: Number,
    reportes: Number,
    permissions: Number,
    acquisitionsData: {
        type: Array,
        required: true
    },
    acquisitionsData2: {type: Array, required: true},
    acquisitionsData3: {type: Array, required: true},
    acquisitionsData4: {type: Array, required: true}
})

const canvas1 = ref(null);
const canvas2 = ref(null);
const canvas3 = ref(null);
const canvas4 = ref(null);
onMounted(() => {
    if (canvas1.value && props.acquisitionsData) {
        createAcquisitionsChart(canvas1.value, props.acquisitionsData);
    }
    if (canvas2.value && props.acquisitionsData2) {
        charSuma(canvas2.value, props.acquisitionsData2);
    }
    if (canvas3.value && props.acquisitionsData3) {
        createAcquisitionsChart3(canvas3.value, props.acquisitionsData3);
    }
    if (canvas4.value && props.acquisitionsData4) {
        createAcquisitionsChart4(canvas4.value, props.acquisitionsData4);

    }
});


</script>
<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <div class="p-4 sm:p-1 lg:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-1 lg:gap-2">
                <!-- Chart 1 -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-1">
                        Tiempos por trabajador (44h/semana o 10560 mins/mes)
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-gray-200 text-center my-1">
                        15 primeros trabajadores
                    </p>
                    <canvas ref="canvas2"></canvas>
                </div>

                <!-- Chart 2 -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                        Actividades (semanal)
                    </h2>
                    <canvas ref="canvas1"></canvas>
                </div>

                <!-- Chart 3 -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                        Actividades (Mensual)
                    </h2>
                    <canvas ref="canvas3"></canvas>
                </div>

                <!-- Chart 4 -->
                <div class=" w-80 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-1">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                        Relacion reportes
                    </h2>
                    <canvas ref="canvas4"></canvas>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
