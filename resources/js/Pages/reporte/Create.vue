<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onBeforeUnmount, onMounted, reactive, watch, watchEffect} from 'vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import '@vuepic/vue-datepicker/dist/main.css';
import {DiferenciaMinutos, formatTime, TransformTdate} from '@/global.ts';
import Elselect from "@/Pages/reporte/elselect.vue";



const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,

    losSelect: Object,
    numberPermissions: Number,
    empleados: Object,
})
const emit = defineEmits(["close"]);
const data = reactive({
    params: {
        pregunta: ''
    },
    actividad_opciones: props.losSelect.actividad,
    paro_opciones: props.losSelect.paro,
    ordenproduccion_opciones: props.losSelect.ordenproduccion,
    reproceso_opciones: props.losSelect.reproceso,
    /*
    fin de los select
     */
    mensajeFalta: '',
    BanderaTipo: true,
})


//very usefull
const justNames = [ //useform fields
    'user_id',
    'actividad_id',
    'reproceso_id',
    'paro_id',
    'ordenproduccion_id',
    
    'tipoReporte',
    'fecha',
    'hora_inicial',
    'hora_final',

    'MinutosEstimados',

];
const form = useForm({...Object.fromEntries(justNames.map(field => [field, '']))});

const disableContextMenu = (event) => {
    // Prevent the default context menu behavior
    event.preventDefault();
    return false;
};

onMounted(() => {
    if (props.numberPermissions > 9) setTimeout(() => ParaElSuper(), 500)
    
    let ordenLocal = localStorage.getItem('ordenproduccion_id')
     if (ordenLocal) {
        form.ordenproduccion_id = props.losSelect.ordenproduccion.find((ele) => {
            return ele.value == ordenLocal 
        })
        console.log("🚀🚀 ~ form.ordenproduccion_id: ", form.ordenproduccion_id);
    }

    document.body.addEventListener('contextmenu', disableContextMenu);
});

//called from onmounted
function ParaElSuper() {
    //esto es para el suchadmin
}


let ValidarNotNull = (campos) => {
    let sonObligatorios = '';
    try {
        campos.forEach((value) => {
            if (typeof form[value] === 'undefined' || form[value] === null || form[value].value === null || form[value].length === 0) {
              console.log("🚀🚀ValidarNotNull ~ form[value]: ", form[value]);
              console.log("🚀🚀ValidarNotNull ~ value: ", value);
               //&& form[value] != ''
                sonObligatorios = value
                throw new Error('BreakException');
            }
        })
    } catch (e) {
        // if (e.message !== 'BreakException') throw e;
    }
    return sonObligatorios;
}

let ValidarCreateReporte = () => {
    let tipo = form.tipoReporte.value;
    let result = true;
    const mensaje = '. Campo obligatorio'

    let horaactual = new Date().getHours()
    let minutosDif = DiferenciaMinutos(horaactual + ':00', form.hora_inicial)

    if (minutosDif > 0) return 'Ha pasado mucho tiempo! refresque la página';

    if (tipo === 0) {
        result = ValidarNotNull([
            'ordenproduccion_id',
            'actividad_id',
        ])
    } //acti

    if (tipo === 1) {
        result = ValidarNotNull([
            'ordenproduccion_id',
            'actividad_id',
            'reproceso_id',
        ])
    } //reproceso

    if (tipo === 2) {
        result = ValidarNotNull([
            'paro_id',
        ])
    } //paro

    let objectMessages = {
        'ordenproduccion_id': 'Orden de Produccion',
        'actividad_id': 'Actividad',
        'reproceso_id': 'Reproceso',
        'paro_id': 'Paro',
    }
    if (result !== '') return objectMessages[result] + mensaje
    else return result
}

// <!--<editor-fold desc="Watchers">-->

watchEffect(() => {
    if (props.show) {

        if (data.BanderaTipo) {

            form.tipoReporte = {title: 'Actividad', value: 0};
            data.BanderaTipo = false
        }

        form.errors = {}
        if (form.fecha === null || form.fecha === '') {
            let currentDate = new Date();
            form.fecha = (TransformTdate(currentDate, '')).substring(0, 10);
            form.hora_inicial = formatTime()

        }

    } else {
        data.BanderaTipo = true
    }
})
watch(() => form.ordenproduccion_id, (newX) => {
    if (newX && newX.value) {
        localStorage.setItem('ordenproduccion_id', newX.value)
        console.log("🚀🚀 ~ se guardo el value:  ", newX.value);
    }
})


watch(() => form.tipoReporte, (newX) => {
    form.actividad_id = null
    form.paro_id = null
    form.user_id = null
    form.ordenproduccion_id = null
    form.reproceso_id = null
    // tipoReporte
    form.MinutosEstimados = null
})
// <!--</editor-fold>-->



// <!--<editor-fold desc="SendToBackend">-->
const create = () => {
    data.mensajeFalta = ValidarCreateReporte();
    form.hora_inicial = formatTime()
    if (data.mensajeFalta === '') {
        setTimeout(SendToBackend(), 200);
    }

}
const SendToBackend = () => {
    form.post(route('reporte.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => alert(JSON.stringify(form.errors, null, 4)),
        onFinish: () => null
    })
    return null
}
    // <!--</editor-fold>-->


//very usefull
const opcinesActividadOTros = [
    {title: 'Actividad', value: 0}, 
    {title: 'Reproceso', value: 1},
    {title: 'Paro', value: 2}
];
</script>

<template>
    <section class="space-y-6  dark:text-white">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'6xl'">
            <form class="px-6 my-8" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div v-if="props.numberPermissions > 1" id="opcinesActividadO" class="xl:col-span-2 col-span-1">
                        <label class=" dark:text-white" name=""> Reportar en nombre de:
                            <small>(Opcional) </small></label>
                        <vSelect :options="props.empleados" label="title" class="dark:bg-gray-400"
                                  v-model="form.user_id" append-to-body></vSelect>
                    </div>
                    <div id="opcinesActividadO" class="xl:col-span-1 col-span-1 ">
                        <label class=" dark:text-white"> Tipo de reporte </label>
                        <vSelect :options="opcinesActividadOTros" label="title" class="dark:bg-gray-400"
                                  v-model="form.tipoReporte" append-to-body></vSelect>
                    </div>
                    <!-- empieza -->

                    <div class="xl:col-span-1 col-span-1">
                        <InputLabel for="fecha" :value="lang().label['fecha']" class=" dark:text-white"/>
                        <TextInput id="fecha" type="date" class=" h-[36px] mt-1 block w-full bg-gray-200  dark:text-white"
                                   v-model="form['fecha']" disabled placeholder="fecha"
                                   :error="form.errors['fecha']"/>
                        <InputError class="mt-2" :message="form.errors['fecha']"/>
                    </div>
                    <div class=" dark:text-white col-span-1">
                        <InputLabel for="hora_inicial"
                                    :value="lang().label['hora inicial']"/>
                        <TextInput id="hora_inicial" type="time" class="mt-1 h-[36px] bg-gray-200 block w-full" disabled
                                   v-model="form['hora_inicial']" placeholder="hora_inicial"
                                   :error="form.errors['hora_inicial']" step="60"/>
                        <InputError class="mt-2" :message="form.errors['hora_inicial']"/>
                    </div>

                    
                    <elselect name="ordenproduccion" :form="form" :data="data" :nombreatipico="'Orden de produccion (OP)'" />
                    <elselect name="actividad" :form="form" :data="data" />

<!--                    tiporeporte = 1 es un reproceso-->
                    <div id="reproceso" v-if="form.tipoReporte.value === 1" class="xl:col-span-2 col-span-1">
                        <label name="reproceso_id" class=" dark:text-white"> Reproceso</label>
                        <vSelect :options="data['reproceso_opciones']" label="title" required append-to-body
                                  v-model="form['reproceso_id']" class="dark:bg-gray-400"
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['reproceso_opciones']"/>
                    </div>
                    
<!--                    tiporeporte = 1 es un paro-->
                    <div id="paro" v-if="form.tipoReporte.value === 2" class="xl:col-span-3  col-span-1">
                        <label name="paro_id" class=" dark:text-white"> Paro</label>
                        <vSelect :options="data['paro_opciones']" label="title" required append-to-body
                                  v-model="form['paro_id']" class="dark:bg-gray-400"
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['paro_id']"/>
                    </div>
                    <!-- termina -->
                </div>


                <div class=" mb-8 mt-[160px] flex justify-end">
                    <h2 v-if="data.mensajeFalta !== ''" class="mx-12 px-8 text-lg font-medium text-red-600 bg-red-50 dark:text-white dark:bg-gray-800">
                        {{ data.mensajeFalta }}
                    </h2>

                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="create">
                        {{ form.processing ? lang().button.add + '...' : lang().button.add }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>

<style>
@reference "../../../css/app.css";

textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}

[name="labelSelectVue"],
[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
