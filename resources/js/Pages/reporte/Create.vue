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



const Hardcoded = [
    '23328-4'
]

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,

    losSelect: Object,
    numberPermissions: Number,
    valuesGoogleCabeza: Object,
    valuesGoogleBody: Object,
    empleados: Object,
})
const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    actividad_id: props.losSelect.actividad,
    centrotrabajo_id: props.losSelect.centrotrabajo,
    disponibilidad_id: props.losSelect.disponibilidad,
    ordentrabajo_id: props.losSelect.ordentrabajo,
    reproceso_id: props.losSelect.reproceso,
    /*
    fin de los select
     */
    temp_disponibilidad_id: null,
    temp_reproceso_id: null,
    temp_actividad_id: null,
    valorInactivo: 'NA',
    cabeza: props.valuesGoogleCabeza,
    nombresOT: Object.values(props.valuesGoogleBody),
    ordentrabajo_ids: [],
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
    'disponibilidad_id',
    'operario_id',
    'ordentrabajo_id',
    'reproceso_id',

    'ordentrabajo_ids',
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
    let elindex = data.ordentrabajo_ids.findIndex((ele) => {
        return ele.title === Hardcoded[0];
    });

    form.ordentrabajo_ids = data.ordentrabajo_ids[elindex];
    form.centrotrabajo_id = data.centrotrabajo_id[2];
    form.actividad_id = data.actividad_id[2];
    data.mensajeTiemposAuto = 'Super administrador';
}

//GetTiempoNotNull:: Selecciona automaticamente un centro que tenga tiempos no nulos
function GetTiempoNotNull() {
    let contador = 0
    form.centrotrabajo_id = data.centrotrabajo_id[1]


    while (data.nombresOT[form.ordentrabajo_ids.value][tiemposEstimados[contador]] === "" && contador <= tiemposEstimados.length) {
        contador++
    }
    form.TiempoEstimado = data.nombresOT[form.ordentrabajo_ids.value][tiemposEstimados[contador]];
    if (contador !== tiemposEstimados.length) {
        form.centrotrabajo_id = data.centrotrabajo_id[contador + 1];
        data.mensajeTiemposAuto = ''
    } else {
        data.mensajeTiemposAuto = 'Tiempos vacios!'
    }

    data.soloUnaVez = false
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
            'ordentrabajo_ids',
            'centrotrabajo_id',
            'actividad_id',
        ])
    } //acti

    if (tipo === 1) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'ordentrabajo_ids',
            'actividad_id',
            'reproceso_id',
        ])
    } //reproceso

    if (tipo === 2) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'disponibilidad_id',
        ])
    } //disponibilidad

    let objectMessages = {
        'ordentrabajo_ids': 'Orden trabajo',
        'actividad_id': 'Actividad',
        'reproceso_id': 'Reproceso',
        'centrotrabajo_id': 'Centro de trabajo',
        'disponibilidad_id': 'Disponibilidad',
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

            data.ordentrabajo_ids = data.nombresOT.map((val, inde) => ({
                title: val.Item?.replace(/_/g, " "),
                value: inde,
                // value2: val.id,
            }))
        }

        //valores implicitos
        if (form.ordentrabajo_ids && form.ordentrabajo_ids.value != null) {
            form.nombreTablero = data.nombresOT[form.ordentrabajo_ids.value][Cabezera[0]]
            form.OTItem = data.nombresOT[form.ordentrabajo_ids.value]['Item']

            if (data.soloUnaVez) {
                GetTiempoNotNull();
            } else {
                //ño
            }
        }
    } else {
        data.BanderaTipo = true
    }
})


watch(() => form.tipoReporte, (newX) => {
    form.actividad_id = null
    form.centrotrabajo_id = null
    form.disponibilidad_id = null
    form.operario_id = null
    form.ordentrabajo_id = null
    form.reproceso_id = null
    form.ordentrabajo_ids = null
    // tipoReporte
    form.otitem = null
    form.nombreTablero = null
    form.OTItem = null
    form.TiempoEstimado = null
})

watch(() => form.ordentrabajo_ids, (newX) => {
    data.soloUnaVez = true
})

watch(() => form.centrotrabajo_id, (newCentro) => { //bookmark: el watcher mas modificado
    if (newCentro && typeof newCentro.value !== 'undefined') {
        let actividadesDelCentro = 'centrotrabajo' + newCentro.title
        data.actividad_id = props.losSelect[actividadesDelCentro]

        if (form.tipoReporte.value !== 2 && form.ordentrabajo_ids) { //si no es una disponibilidad

            console.log("=>(Create.vue:299) form.centrotrabajo_id.title", form.centrotrabajo_id.title);
            switch (form.centrotrabajo_id.title) {
                case 'CORTE': data.tempCentro = 0; 
                break;
                case 'DOBLEZ': data.tempCentro = 1; 
                break;
                case 'SOLDADURA': data.tempCentro = 2; 
                break;
                case 'PULIDA': data.tempCentro = 3; 
                break;
                case 'ENSAMBLE': data.tempCentro = 4; 
                break;
                case 'COBRE': data.tempCentro = 5; 
                break;
                case 'CABLEADO': data.tempCentro = 6; 
                break;
                case 'INGENIERIA MECANICA': data.tempCentro = 7; 
                break;
                case 'INGENIERIA ELECTRICA': data.tempCentro = 8; 
                break;
                case 'FRENTE MUERTO': data.tempCentro = 9; 
                break; //todo: si hay un centro nuevo
                case 'TIEMPO AMARILLADO': data.tempCentro = 10;break; 
                case 'TIEMPO PRUEBAS': data.tempCentro = 11;break; 
            }
            // data.tempCentro = form.centrotrabajo_id.value - 1 //esto esta horrible con id
            
            form.TiempoEstimado = data.nombresOT[form.ordentrabajo_ids.value][tiemposEstimados[data.tempCentro]];
// console.log("resultado", data.nombresOT[form.ordentrabajo_ids.value][tiemposEstimados[data.tempCentro]]);
console.table( data.nombresOT[form.ordentrabajo_ids.value]);
console.table( tiemposEstimados);
console.log("=>(Create.vue:305) data.tempCentro", data.tempCentro);
console.table( data.centrotrabajo_id);
        } else {
            // form.ordentrabajo_id =
            console.log(form.ordentrabajo_ids) //nuevo requerimiento 2dic2023: se pondra dos digitos del año seguido de 000
        }
    }
    form.actividad_id = {title: 'Seleccione actividad', value: null}
})
// <!--</editor-fold>-->


// <!--<editor-fold desc="SendToBackend">-->
    const create = () => {
    form.ordentrabajo_id = form.ordentrabajo_ids
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
const opcinesActividadOTros = [{title: 'Actividad', value: 0}, {
    title: 'Reproceso',
    value: 1
}, {title: 'Disponibilidad(paro)', value: 2}];
const arrayMostrarDelCodigo = ['Nombre Tablero', '% avance', 'OT+Item', 'Tiempo estimado'];
const Cabezera = ['Nombre_tablero', 'avance'];
</script>

<template>
    <!--    <meta http-equiv="refresh" content="120">-->

    <section class="space-y-6  dark:text-white">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'4xl'">
            <form class="px-6 my-8" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    <div v-if="props.numberPermissions > 1" id="opcinesActividadO" class="xl:col-span-2 col-span-1">
                        <label class=" dark:text-white" name=""> Reportar en nombre de:
                            <small>(Opcional) </small></label>
                        <vSelect :options="props.empleados" label="title" class="dark:bg-gray-400"
                                  v-model="form.user_id" append-to-body></vSelect>
                    </div>
                    <div id="opcinesActividadO" class="xl:col-span-2 col-span-1 ">
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

                    <div id="Sordentrabajo" v-if="form.tipoReporte.value !== 2" class="xl:col-span-2 col-span-1">
                        <label name="ordentrabajo_ids" class=" dark:text-white"> Orden de trabajo </label>
                        <vSelect :options="data['ordentrabajo_ids']" label="title" class="dark:bg-gray-400"
                                  v-model="form['ordentrabajo_ids']" append-to-body
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['ordentrabajo_id']"/>
                    </div>

                    <div v-if="form.ordentrabajo_ids && form.tipoReporte.value !== 2" class="w-full lg:col-span-2 col-span-1  dark:text-white">
                        <InputLabel :for="index" :value="arrayMostrarDelCodigo[0]" class=""/>
                        <TextInput :id="index" type="text" disabled class="mt-1 h-[36px] block w-full bg-gray-200"
                                   :value="data.nombresOT[form.ordentrabajo_ids.value][Cabezera[0]]"
                        />
                    </div>

<!--                    avanze-->
                    <div v-if="form.ordentrabajo_ids && form.tipoReporte.value !== 2" class="w-full col-span-1 dark:text-white">
                        <InputLabel :for="index" :value="arrayMostrarDelCodigo[1]"/>
                        <TextInput :id="index" type="text" disabled class="mt-1 h-[36px] block w-full bg-gray-200"
                                   :value="data.nombresOT[form.ordentrabajo_ids.value][Cabezera[1]]"
                        />
                    </div>

                  


                    <!-- tiempo estimado -->
                    <div v-if="form.ordentrabajo_ids && form.centrotrabajo_id && form.tipoReporte.value !== 2" class=" col-span-1 dark:text-white">
                        <InputLabel :for="index" :value="arrayMostrarDelCodigo[3]"/>
                        <TextInput :id="index" type="text" disabled class="mt-1 h-[36px] block w-full bg-gray-200 dark:bg-gray-400 dark:text-white"
                                   v-model="form.TiempoEstimado"
                        />
                    </div>
                    <div id="Scentrotrabajo" class=" xs:col-span-1 md:col-span-2">
                        <label name="centrotrabajo_id" class=" dark:text-white"> Centro de trabajo </label>
                        <vSelect :options="data['centrotrabajo_id']" label="title" class="dark:bg-gray-400"
                                  v-model="form['centrotrabajo_id']" append-to-body
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['centrotrabajo_id']"/>
                    </div>

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
                    <div id="disponibilidad" v-if="form.tipoReporte.value === 2" class="xl:col-span-3  col-span-1">
                        <label name="disponibilidad_id" class=" dark:text-white"> Disponibilidad</label>
                        <vSelect :options="data['disponibilidad_id']" label="title" required append-to-body
                                  v-model="form['disponibilidad_id']" class="dark:bg-gray-400"
                        ></vSelect>
                        <InputError class="mt-2" :message="form.errors['disponibilidad_id']"/>
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
