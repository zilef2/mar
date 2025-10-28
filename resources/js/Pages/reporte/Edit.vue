<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {onMounted, reactive, watch, watchEffect} from 'vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';

import {LookForValueInArray, TransformTdate, formatTime} from '@/global.ts';

const props = defineProps({
    show: Boolean,
    title: String,
    generica: Object,
    losSelect: Object,
    numberPermissions: Number,
    valuesGoogleCabeza: Object,
    valuesGoogleBody: Object,
    Trabajadores: Object,
})

const emit = defineEmits(["close"]);

const opcinesActividadOTros = [{title: 'Actividad', value: 0}, {
    title: 'Reproceso',
    value: 1
}, {title: 'Disponibilidad(paro)', value: 2}];
const arrayMostrarDelCodigo = ['Nombre Tablero', '% avance', 'OT+Item', 'Tiempo estimado'];
const Cabezera = ['Nombre_tablero', 'avance'];


const data = reactive({
    params: {
        pregunta: ''
    },
    // tipoReporte:{ title: 'Actividad', value: 0 },
    actividad_id: props.losSelect.actividad,
    centrotrabajo_id: props.losSelect.centrotrabajo,
    disponibilidad_id: props.losSelect.disponibilidad,
    ordentrabajo_id: props.losSelect.ordentrabajo,
    ordenTemporal: [],
    reproceso_id: props.losSelect.reproceso,
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
    errorTranscurrido: '',
})


//very usefull
const justNames = [
    // 'codigo',
    'tipoReporte',
    'fecha',
    'hora_inicial',
    'hora_final',
    'tiempo_transcurrido',
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

onMounted(() => {
    if (props.numberPermissions > 9) {
        // const valueRAn = Math.floor(Math.random() * (9 - 0) + 0)
        // form.codigo = 'AdminCod'+ (valueRAn);
        // form.hora_inicial = '0'+valueRAn+':00'//temp
        // form.fecha = '2023-06-01'
        // form.centrotrabajo_id = 1
        // form.actividad_id = 1
    }

});

const tiemposEstimados = [
    "Tiempo_estimado_corte",
    "Tiempo_estimado_doblez", //2
    "Tiempo_estimado_soldadura",
    "Tiempo_estimado_pulida",
    "Tiempo_estimado_ensamble", //5
    "Tiempo_estimado_cobre",
    "Tiempo_estimado_cableado",//7
    "Tiempo_estimado_Ing_mec",
    "Tiempo_estimado_Ing_elec", //9
];

// <!--<editor-fold desc="watch and watcheffect">-->
watchEffect(() => {
    if (props.show) {
        form.errors = {}

        RecuperarForm();

    } else {
        data.BanderaTipo = true
    }
})

function RecuperarForm() {
    form.centrotrabajo_id = {title: props.generica.centrotrabajo_s, value: props.generica.centrotrabajo_id}

    if (form.ordentrabajo_ids?.value) {
        form.nombreTablero = data.nombresOT[form.ordentrabajo_ids.value][Cabezera[0]];
        form.OTItem = data.nombresOT[form.ordentrabajo_ids.value]['Item'];

        // let tempCentro = form.centrotrabajo_id?.value - 1;
        // console.log("=>(Edit.vue:131) tempCentro", tempCentro);
        // form.TiempoEstimado = data.nombresOT[form.ordentrabajo_ids.value][tiemposEstimados[tempCentro]];
    }
    form.TiempoEstimado = props.generica.TiempoEstimado
    form.actividad_id = props.generica.actividad_id
    form.hora_final = props.generica.hora_final
    form.actividad_id = {title: props.generica.actividad_s, value: props.generica.actividad_id}
    form.user_id = {title: props.generica.operario_s, value: props.generica.user_id}
}


watch(() => form.ordentrabajo_ids, (newX) => {
    data.soloUnaVez = true
    if (newX && form.tipoReporte.value !== 2 && form.ordentrabajo_ids) { //si no es una disponibilidad
        data.tempCentro = form.centrotrabajo_id?.value - 1
        form.TiempoEstimado = data.nombresOT[form.ordentrabajo_ids.value][tiemposEstimados[data.tempCentro]];
    } else {
        // form.ordentrabajo_id =
        console.log(form.ordentrabajo_ids) //se pondra dos digitos del año seguido de 000
    }
})
watch(() => form.hora_inicial, (newX) => {
    if (form.hora_final) {
        form.tiempo_transcurrido = diferenciaHoras(newX, form.hora_final)
        console.log("=>(Edit.vue:250) form.tiempo_transcurrido", form.tiempo_transcurrido);
    }
})
watch(() => form.hora_final, (newX) => {
    if (form.hora_inicial) {
        form.tiempo_transcurrido = diferenciaHoras(form.hora_inicial, newX)
        console.log("=>(Edit.vue:250) form.tiempo_transcurrido", form.tiempo_transcurrido);
    }
})

function floorDecimal(numero, decimales = 1) {
    const factor = Math.pow(10, decimales);
    return Math.floor(numero * factor) / factor;
}

const diferenciaHoras = (horaInicial, horaFinal) => {
    const [h1, m1] = horaInicial.split(":").map(Number);
    const [h2, m2] = horaFinal.split(":").map(Number);

    let minutosInicio = h1 * 60 + m1;
    let minutosFinal = h2 * 60 + m2;

    let diferencia = minutosFinal - minutosInicio;
    console.log("=>(Edit.vue:273) diferencia", diferencia);
    if (diferencia < 0) {
        data.errorTranscurrido = 'La hora inicial debe ser anterior a la final'
        return "0"; // Evitar valores negativos
    }
    data.errorTranscurrido = ''
    let horas = Math.floor(diferencia / 60);
    console.log("=>(Edit.vue:279) diferencia", diferencia);
    let minutos = floorDecimal((diferencia % 60) / 60, 2);
    console.log("=>(Edit.vue:281) minutos", minutos);
    // let minutos = Math.floor10(diferencia / 3600,-1);

    return horas + minutos;
    // return `${String(horas).padStart(2, "0")}:${String(minutos).padStart(2, "0")}`;
};

watch(() => props.show, () => {

    if (data.BanderaTipo) {

        data.BanderaTipo = false
        form.tipoReporte = opcinesActividadOTros[props.generica?.tipoReporte]
        data.ordentrabajo_ids = data.nombresOT.map((val, inde) => ({
            title: val.Item?.replace(/_/g, " "),
            value: inde,
        }))
        data.ordenTemporal = data.nombresOT.map((val, inde) => ({
            value: inde,
            valueID: val.id,
        }))

        let posicionOrden = data.ordenTemporal.findIndex(obj => obj.valueID == props.generica?.ordentrabajo_id)

        form.ordentrabajo_ids = data.ordentrabajo_ids[posicionOrden]
        // form.ordentrabajo_id = data.ordentrabajo_ids[posicionOrden]['title']

        form.centrotrabajo_id = data.centrotrabajo_id[props.generica?.centrotrabajo_id]

        form.actividad_id = data.actividad_id[props.generica?.actividad_id] ? data.actividad_id[props.generica?.actividad_id] : null
        form.disponibilidad_id = data.disponibilidad_id[props.generica?.disponibilidad_id] ? data.disponibilidad_id[props.generica?.disponibilidad_id] : null
        form.reproceso_id = data.reproceso_id[props.generica?.reproceso_id] ? data.reproceso_id[props.generica?.reproceso_id] : null

        form.cantidad = props.generica?.cantidad
        form.fecha = props.generica?.fecha
        form.hora_inicial = props.generica?.hora_inicial


        let posicionUser = props.Trabajadores.findIndex(obj => obj.value == props.generica?.operario_id)
        form.user_id = props.Trabajadores[posicionUser]

        form.nombreTablero = props.generica?.nombreTablero
        form.OTItem = props.generica?.OTItem
        form.TiempoEstimado = props.generica?.TiempoEstimado

    } else {
        data.BanderaTipo = !props.show
    }
})

watch(() => form.centrotrabajo_id, (newCentro, old) => {
    if (newCentro && typeof newCentro.value !== 'undefined') {
        let actividadesDelCentro = 'centrotrabajo' + newCentro.title
        data.actividad_id = props.losSelect[actividadesDelCentro]


        if (newCentro.value !== props.generica.centrotrabajo_id) {

            form.actividad_id = {title: 'Seleccione actividad', value: null}
        }
        // console.log("🧈 debu props.generica.centrotrabajo_id:", props.generica.centrotrabajo_id);
        // console.log("🧈 debu newCentro.value:", newCentro);
        // console.log("🧈 debu old:", old);
    }
})
// <!--</editor-fold>-->



let ValidarNotNull = (campos) => {
    let sonObligatorios = '';
    try {
        campos.forEach((value, i) => {
            if (typeof form[value] === 'undefined' || form[value] === null || form[value].length === 0) { //&& form[value] != ''
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
    const mensaje = ' es obligatorio'
    if (tipo == 0) {
        result = ValidarNotNull([
            'ordentrabajo_ids',
            'centrotrabajo_id',
            'actividad_id',
        ])
    } //acti

    if (tipo == 1) {
        result = ValidarNotNull([
            'centrotrabajo_id',
            'ordentrabajo_ids',
            'actividad_id',
            'reproceso_id',
        ])
    } //reproceso

    if (tipo == 2) {
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
    if (result != '') return objectMessages[result] + mensaje
    else return result
}

const update = () => {
    if (data.mensajeFalta === '' && data.errorTranscurrido === '') {
        form.ordentrabajo_id = form.ordentrabajo_ids
        // data.mensajeFalta = ValidarCreateReporte();
        console.log("🧈 debu form.tipoReporte.value:", form.tipoReporte.value);
        data.mensajeFalta = ValidarCreateReporte();

        let StringResultAny;
        StringResultAny = LookForValueInArray(props.losSelect.centrotrabajo, form.centrotrabajo_id)
        form.centrotrabajo_id = StringResultAny != '' ? StringResultAny : '';

        if (form.tipoReporte.value === 0) {
            StringResultAny = LookForValueInArray(props.losSelect.actividad, form.actividad_id)
            form.actividad_id = StringResultAny != '' ? StringResultAny : '';
        }
        if (form.tipoReporte.value === 1) {
            StringResultAny = LookForValueInArray(props.losSelect.actividad, form.actividad_id)
            form.actividad_id = StringResultAny != '' ? StringResultAny : '';

            StringResultAny = LookForValueInArray(props.losSelect.reproceso, form.reproceso_id)
            form.reproceso_id = StringResultAny != '' ? StringResultAny : '';
        }

        if (form.tipoReporte.value === 2) { //disponibilidad
            StringResultAny = LookForValueInArray(props.losSelect.disponibilidad, form.disponibilidad_id)
            form.disponibilidad_id = StringResultAny != '' ? StringResultAny : '';
        }


        form.put(route('reporte.update', props.generica?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => alert(JSON.stringify(form.errors, null, 4)),
            onFinish: () => null,
        })
    }
}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close'), data.BanderaTipo = true" maxWidth="7xl">
            <form class="px-6 my-8" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div v-if="props.numberPermissions > 8" id="opcinesActividadO"
                         class="xl:col-span-2 col-span-1">
                        <label name=""> Reportar en nombre de: </label>
                        <v-select :options="props.Trabajadores" disabled label="title"
                                  v-model="form.user_id" class="bg-gray-500"></v-select>
                    </div>
                    <div id="opcinesActividadO" class="xl:col-span-2 col-span-1">
                        <label name=""> Tipo de reporte </label>
                        <v-select :options="opcinesActividadOTros" label="title" disabled
                                  v-model="form.tipoReporte"></v-select>
                    </div>
                    <!-- empieza -->

                    <div class="xl:col-span-1 col-span-1">
                        <InputLabel for="fecha" :value="lang().label['fecha']"/>
                        <TextInput id="fecha" type="date" class="mt-1 block w-full bg-gray-200" v-model="form['fecha']"
                                   placeholder="fecha" :error="form.errors['fecha']"/>
                        <InputError class="mt-2" :message="form.errors['fecha']"/>
                    </div>
                    <div class=" col-span-1">
                        <InputLabel for="hora_inicial" :value="lang().label['hora inicial']"/>
                        <TextInput id="hora_inicial" type="time"
                                   v-model="form['hora_inicial']" placeholder="hora_inicial"
                                   :error="form.errors['hora_inicial']" step="60"
                                   class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" :message="form.errors['hora_inicial']"/>
                    </div>
                    <div class=" col-span-1">
                        <InputLabel for="hora_final" :value="lang().label['hora_final']"/>
                        <TextInput id="hora_final" type="time"
                                   v-model="form['hora_final']" placeholder="hora_final"
                                   :error="form.errors['hora_final']" step="60"
                                   class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" :message="form.errors['hora_final']"/>
                    </div>
                    <div class=" col-span-1">
                        <InputLabel for="tiempo_transcurrido" :value="lang().label['tiempo_transcurrido']"/>
                        <TextInput id="tiempo_transcurrido" type="text" placeholder="tiempo transcurrido" disabled
                                   v-model="form['tiempo_transcurrido']"
                                   class="mt-1 block w-full bg-gray-300"
                        />
                    </div>

                    <div id="Sordentrabajo" v-if="form.tipoReporte.value != 2" class="xl:col-span-2 col-span-1">
                        <label name="ordentrabajo_ids"> Orden de trabajo </label>
                        <v-select :options="data['ordentrabajo_ids']" label="title"
                                  v-model="form['ordentrabajo_ids']"></v-select>
                        <InputError class="mt-2" :message="form.errors['ordentrabajo_id']"/>
                    </div>

                    <div v-if="form.ordentrabajo_ids && form.tipoReporte.value != 2"
                         class="w-full lg:col-span-2 col-span-1">
                        <InputLabel for="index" :value="arrayMostrarDelCodigo[0]"/>
                        <TextInput id="index" type="text" disabled class="mt-1 block w-full bg-gray-200"
                                   :value="data.nombresOT[form.ordentrabajo_ids.value][Cabezera[0]]"/>
                    </div>

                    <div v-if="form.ordentrabajo_ids && form.tipoReporte.value != 2" class="w-full col-span-1">
                        <InputLabel for="index" :value="arrayMostrarDelCodigo[1]"/>
                        <TextInput id="index" type="text" disabled class="mt-1 block w-full bg-gray-200"
                                   :value="data.nombresOT[form.ordentrabajo_ids.value][Cabezera[1]]"/>
                    </div>

                    <div id="Scentrotrabajo" class=" col-span-1">
                        <label name="centrotrabajo_id"> Centro de trabajo </label>
                        <v-select :options="data['centrotrabajo_id']" label="title"
                                  v-model="form['centrotrabajo_id']"></v-select>
                        <InputError class="mt-2" :message="form.errors['centrotrabajo_id']"/>
                    </div>


                    <!-- tiempo estimado -->
                    <div v-if="form.ordentrabajo_ids && form.centrotrabajo_id && form.tipoReporte.value !== 2"
                         class=" col-span-1">
                        <InputLabel for="index" :value="arrayMostrarDelCodigo[3]"/>
                        <TextInput id="index" type="text" disabled class="mt-1 block w-full bg-gray-200"
                                   v-model="form.TiempoEstimado"/>
                    </div>

                    <!-- eleccion -->
                    <div id="Sactividad" v-if="form.tipoReporte.value == 0 || form.tipoReporte.value == 1"
                         class="xl:col-span-1 col-span-1">
                        <label name="label_actividad_id"> Actividad </label>
                        <v-select :options="data['actividad_id']" label="title" required
                                  v-model="form.actividad_id"></v-select>
                        <InputError class="mt-2" :message="form.errors['actividad_id']"/>
                    </div>
                    <div id="Sreproceso" v-if="form.tipoReporte.value == 1" class="xl:col-span-1 col-span-1">
                        <label name="reproceso_id"> Reproceso</label>
                        <v-select :options="data['reproceso_id']" label="title" required
                                  v-model="form['reproceso_id']"></v-select>
                        <InputError class="mt-2" :message="form.errors['reproceso_id']"/>
                    </div>
                    <div id="Sdisponibilidad" v-if="form.tipoReporte.value == 2" class="xl:col-span-1 col-span-1">
                        <label name="disponibilidad_id"> Disponibilidad</label>
                        <v-select :options="data['disponibilidad_id']" label="title" required
                                  v-model="form['disponibilidad_id']"></v-select>
                        <InputError class="mt-2" :message="form.errors['disponibilidad_id']"/>
                    </div>
                    <!-- termina -->
                </div>


                <div class=" mb-8 mt-[20px] flex justify-end">
                    <h2 v-if="data.mensajeFalta != ''"
                        class="mx-12 px-8 text-lg font-medium text-red-600 dark:text-red-200">
                        {{ data.mensajeFalta }}
                    </h2>
                    <h2 v-if="data.mensajeTiemposAuto != ''"
                        class="mx-12 px-8 text-lg font-medium text-gray-800 dark:text-gray-100">
                        {{ data.mensajeTiemposAuto }}
                    </h2>
                    <h2 v-if="data.errorTranscurrido != ''"
                        class="mx-12 px-8 text-lg font-medium text-red-600 dark:text-red-200">
                        {{ data.errorTranscurrido }}
                    </h2>

                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
                <div class=" mb-8 mt-[300px] flex justify-end">.
                </div>
            </form>
        </Modal>
    </section>
</template>

<style>
textarea {
    @apply px-3 py-2 border border-gray-300 rounded-md;
}

[name="labelSelectVue"],
.muted {
    color: #1b416699;
}

[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
