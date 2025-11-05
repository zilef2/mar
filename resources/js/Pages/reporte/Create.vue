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
    actividad_id: props.losSelect.actividad,
    centrotrabajo_id: props.losSelect.centrotrabajo,
    paro_id: props.losSelect.paro,
    ordenproduccion_id: props.losSelect.ordenproduccion,
    reproceso_id: props.losSelect.reproceso,
    /*
    fin de los select
     */
    temp_paro_id: null,
    temp_reproceso_id: null,
    temp_actividad_id: null,
    valorInactivo: 'NA',
    cabeza: props.valuesGoogleCabeza,
    ordenproduccion_ids: [],
    mensajeFalta: '',
    BanderaTipo: true,
    mensajeTiemposAuto: '',
    soloUnaVez: true,
    limiteMinimo: '',
    tempCentro: 0,
})


//very usefull
const justNames = [
    // 'codigo',
    'tipoReporte',
    'fecha',
    'hora_inicial',
    'hora_final',
    'actividad_id',
    'centrotrabajo_id',
    'paro_id',
    'user_id',
    'ordenproduccion_id',
    'reproceso_id',

    'ordenproduccion_ids',
    'otitem',
    'user_id',

    'nombreTablero',
    'OTItem',
    'TiempoEstimado',

];
const form = useForm({...Object.fromEntries(justNames.map(field => [field, '']))});

const CalcularHoraActual = () => {
    let horaActual = new Date();
    horaActual.setHours(horaActual.getHours() - 1);
    let formatoHora = (valor) => (valor < 10 ? `0${valor}` : valor);
    return `${formatoHora(horaActual.getHours())}:${formatoHora(horaActual.getMinutes())}`;
}

const disableContextMenu = (event) => {
    // Prevent the default context menu behavior
    event.preventDefault();
    return false;
};

onMounted(() => {
    data.limiteMinimo = CalcularHoraActual()
    setInterval(() => {
        data.limiteMinimo = CalcularHoraActual()
    }, 60000);

    if (props.numberPermissions > 9) {
        setTimeout(() => ParaElSuper(), 1500)
    }

    // document.body.addEventListener('contextmenu', this.disableContextMenu);
    document.body.addEventListener('contextmenu', disableContextMenu);
});

onBeforeUnmount(() => {
    // document.body.removeEventListener('contextmenu', this.disableContextMenu);
})

const tiemposEstimados = [
    "Tiempo_estimado_corte",//but contador = 0
    "Tiempo_estimado_doblez", //2
    "Tiempo_estimado_soldadura",
    "Tiempo_estimado_pulida",
    "Tiempo_estimado_ensamble", //5
    "Tiempo_estimado_cobre",
    "Tiempo_estimado_cableado",//7
    "Tiempo_estimado_Ing_mec",
    "Tiempo_estimado_Ing_elec", //9
    "Tiempo_estimado_FM", //10
    "Tiempo_Amarillado", //11?
    "Tiempo_pruebas", //12?
];


function ParaElSuper() {
    let elindex = data.ordenproduccion_ids.findIndex((ele) => {
        return ele.title === Hardcoded[0];
    });

    form.ordenproduccion_ids = data.ordenproduccion_ids[elindex];
    form.centrotrabajo_id = data.centrotrabajo_id[2];
    form.actividad_id = data.actividad_id[2];
    data.mensajeTiemposAuto = 'Super administrador';
}


let ValidarNotNull = (campos) => {
    let sonObligatorios = '';
    try {
        campos.forEach((value) => {
            if (typeof form[value] === 'undefined' || form[value] === null || form[value].value === null || form[value].length === 0) { //&& form[value] != ''
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
    console.log(horaactual)
    console.log('espacio\n')
    console.log(form.hora_inicial)
    console.log('espacio2\n')
    let minutosDif = DiferenciaMinutos(horaactual + ':00', form.hora_inicial)
    console.log(minutosDif)

    if (minutosDif > 0) return 'Ha pasado mucho tiempo!';

    if (tipo === 0) {
        result = ValidarNotNull([
            'ordenproduccion_ids',
            'centrotrabajo_id',
            'actividad_id',
        ])
    } //acti

    if (tipo === 1) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'ordenproduccion_ids',
            'actividad_id',
            'reproceso_id',
        ])
    } //reproceso

    if (tipo === 2) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'paro_id',
        ])
    } //paro

    let objectMessages = {
        'ordenproduccion_ids': 'Orden trabajo',
        'actividad_id': 'Actividad',
        'reproceso_id': 'Reproceso',
        'centrotrabajo_id': 'Centro de trabajo',
        'paro_id': 'paro',
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


watch(() => form.tipoReporte, (newX) => {
    form.actividad_id = null
    form.centrotrabajo_id = null
    form.paro_id = null
    form.user_id = null
    form.ordenproduccion_id = null
    form.reproceso_id = null
    form.ordenproduccion_ids = null
    // tipoReporte
    form.otitem = null
    form.nombreTablero = null
    form.OTItem = null
    form.TiempoEstimado = null
})

watch(() => form.ordenproduccion_ids, (newX) => {
    data.soloUnaVez = true
})

watch(() => form.centrotrabajo_id, (newCentro) => { //bookmark: el watcher mas modificado
    if (newCentro && typeof newCentro.value !== 'undefined') {
        let actividadesDelCentro = 'centrotrabajo' + newCentro.title
        data.actividad_id = props.losSelect[actividadesDelCentro]

        if (form.tipoReporte.value !== 2 && form.ordenproduccion_ids) { //si no es una paro

            
        } else {
            console.log(form.ordenproduccion_ids) //nuevo requerimiento 2dic2023: se pondra dos digitos del año seguido de 000
        }
    }
    form.actividad_id = {title: 'Seleccione actividad', value: null}
})
// <!--</editor-fold>-->


// <!--<editor-fold desc="SendToBackend">-->
    const create = () => {
    form.ordenproduccion_id = form.ordenproduccion_ids
    data.mensajeFalta = ValidarCreateReporte();
    form.hora_inicial = formatTime()
    if (data.mensajeFalta === '') {
        setTimeout(SendToBackend(), 500);
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
    <!--    <meta http-equiv="refresh" content="120">-->

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
                    <!--                        :value="lang().label['hora inicial'] + ', min: '+data.limiteMinimo" />-->
                    <div class=" dark:text-white col-span-1">
                        <InputLabel for="hora_inicial"
                                    :value="lang().label['hora inicial']"/>
                        <TextInput id="hora_inicial" type="time" class="mt-1 h-[36px] bg-gray-200 block w-full" disabled
                                   v-model="form['hora_inicial']" placeholder="hora_inicial"
                                   :error="form.errors['hora_inicial']" step="60"/>
                        <InputError class="mt-2" :message="form.errors['hora_inicial']"/>
                    </div>

                    asdasdas {{props.ordenproduccion}}
                    <elselect name="orden_produccion" :form="form" :data="data" />

                    <!-- eleccion -->
                    <div id="actividad" v-if="form.tipoReporte.value === 0 || form.tipoReporte.value === 1" class="xl:col-span-2 col-span-1">
                        <label name="actividad_id" class=" dark:text-white"> Actividad </label>
                        <vSelect :options="data['actividad_id']" label="title" required
                                  v-model="form['actividad_id']" class="dark:bg-gray-400" append-to-body
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['actividad_id']"/>
                    </div>
                    <div id="reproceso" v-if="form.tipoReporte.value === 1" class="xl:col-span-2 col-span-1">
                        <label name="reproceso_id" class=" dark:text-white"> Reproceso</label>
                        <vSelect :options="data['reproceso_id']" label="title" required append-to-body
                                  v-model="form['reproceso_id']" class="dark:bg-gray-400"
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['reproceso_id']"/>
                    </div>
                    <div id="paro" v-if="form.tipoReporte.value === 2" class="xl:col-span-3  col-span-1">
                        <label name="paro_id" class=" dark:text-white"> Paro</label>
                        <vSelect :options="data['paro_id']" label="title" required append-to-body
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
                    <h2 v-if="data.mensajeTiemposAuto !== ''" class="mx-12 px-8 text-lg font-medium text-gray-800 dark:text-white dark:bg-gray-800">
                        {{ data.mensajeTiemposAuto }}
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
