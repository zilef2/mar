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
        <div class="grid grid-cols-1 gap-16">
            <div style="width: 1200px; margin: auto;">
                <h2 class="text-xl my-4 text-center font-medium">Tiempos por trabajador (44h/semana o 2640 mins)</h2>
                <canvas ref="canvas2"></canvas>
            </div>
            <div style="width: 1200px; margin: auto;">
                <h2 class="text-xl my-4 text-center font-medium">Actividades (semanal)</h2>
                <canvas ref="canvas1"></canvas>
            </div>
            <div style="width: 1200px; margin: auto;">
                <h2 class="text-xl my-4 text-center font-medium">Actividades (Mesual)</h2>
                <canvas ref="canvas3"></canvas>
            </div>
            <div style="width: 800px; margin: auto;"><h2 class="text-xl my-4 text-center font-medium">Relacion reportes</h2><canvas ref="canvas4"></canvas></div>
        </div>
<!--        <Breadcrumb :title="'Resumen'" :breadcrumbs="[]"/>-->
<!--        <div class="space-y-4">-->
<!--            <div-->
<!--                class="text-white dark:text-gray-100 grid grid-cols-1 md:grid-cols-3 2xl:grid-cols-4 gap-2 sm:gap-4 overflow-hidden shadow-sm">-->
<!--                <div v-for="(modelo, index) in dashboard" :key="index">-->
<!--                    <div-->
<!--                        class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between items-center overflow-hidden"-->
<!--                        :class="colores[index]">-->
<!--                        <div class="flex flex-col">-->
<!--                            <p class="text-4xl font-bold">{{ props.contadores[modelo] }}</p>-->
<!--                            <p class="text-md md:text-lg uppercase">{{ lang().label[modelo] }}</p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div-->
<!--                        class="rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-sky-600 dark:hover:bg-black"-->
<!--                        :class="colores[index]"-->
<!--                    >-->
<!--                        <Link :href="route(dashSinS[index]+'.index')" class="flex justify-between items-center">-->
<!--                            <p>{{ lang().label.more }}</p>-->
<!--                            <ChevronRightIcon class="w-5 h-5"/>-->
<!--                        </Link>-->
<!--                    </div>-->
<!--                </div>-->


<!--                <div v-if="can(['isSuper'])">-->
<!--                    <div-->
<!--                        class="rounded-t-none sm:rounded-t-lg px-4 py-6 flex justify-between bg-amber-600/70 dark:bg-amber-500/80 items-center overflow-hidden">-->
<!--                        <div class="flex flex-col">-->
<!--                            <p class="text-4xl font-bold">{{ props.contadores.permissions }}</p>-->
<!--                            <p class="text-md md:text-lg uppercase">{{ lang().label.permission }}</p>-->
<!--                        </div>-->
<!--                        &lt;!&ndash; <div>-->
<!--                            <ShieldCheckIcon class="w-16 h-auto" />-->
<!--                        </div> &ndash;&gt;-->
<!--                    </div>-->
<!--                    <div-->
<!--                        class="bg-amber-600 dark:bg-amber-600/80 rounded-b-none sm:rounded-b-lg p-2 overflow-hidden hover:bg-amber-600/90 dark:hover:bg-amber-600/70">-->
<!--                        <Link :href="route('permission.index')" class="flex justify-between items-center">-->
<!--                            <p>{{ lang().label.more }}</p>-->
<!--                            <ChevronRightIcon class="w-5 h-5"/>-->
<!--                        </Link>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </AuthenticatedLayout>
</template>
