<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import vSelect from "vue-select"; import "vue-select/dist/vue-select.css";
import { reactive, watch, onMounted, ref, watchEffect } from 'vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

// --------------------------- ** -------------------------

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

    numberPermissions: Number,
})
const emit = defineEmits(["close"]);

const data = reactive({
    params: {
        pregunta: ''
    },
    centros : [0],
    valido: true,
    mensajeError : ''
})

//very usefull
const justNames = props.titulos.map(names => names['order'])
const form = useForm({ ...Object.fromEntries(justNames.map(field => [field, ''])),
    centro_id: [props.losSelect[0]]
});
onMounted(() => {});

const printForm = [];
props.titulos.forEach(names =>
    printForm.push({
        idd: names['order'], label: names['label'], type: names['type']
        //, value: form[names['order']]
    })
);

// const printForm = [
//     { idd: justNames[0], label: 'codigo', type: 'text', value: form.codigo , elif:null},
//     { idd: 'nombre', label: 'nombre', type: 'text', value: form.nombre , elif:null},
// ];
// console.log("🧈✅ debu son iguales:", JSON.stringify(printForm) === JSON.stringify(printForm2));

let validar = () => {
    try{
        if(form.nombre) data.valido = true
    } catch (e) {
        // if (e.message !== 'BreakException') throw e;
    }
}

const create = () => {
    validar()
    if(data.valido){
        form.post(route('reproceso.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    }else{
        data.mensajeError = 'Hay elementos vacios'
    }
}

watchEffect(() => {
    if (props.show) {
        form.errors = {}
    }
})

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!--                    <div v-for="(centro, index) in data.centros" id="SelectVue">-->
<!--                        <label name="labelSelectVue"> Centro de trabajo </label>-->
<!--                        <vSelect :options="props.losSelect" label="title"-->
<!--                                  v-model="form.centro_id[index]"></vSelect>-->
<!--                        <InputError class="mt-2" :message="form.errors.centro_id" />-->
<!--                    </div>-->
                    <div class="">
                        <InputLabel for="nombre" :value="lang().label.nombre" />
                        <TextInput id="nombre" type="text" class="mt-1 block w-full"
                                   v-model="form['nombre']" required :placeholder="nombre"
                                   :error="form.errors['nombre']" />
                        <InputError class="mt-2" :message="form.errors['nombre']" />
                    </div>
                </div>
<!--                <div class="flex my-5 gap-8">-->
<!--                    <PrimaryButton type="button" :disabled="form.processing" @click="nuevoHijo()"> Mas centros </PrimaryButton>-->
<!--                    <PrimaryButton type="button" :disabled="form.processing" @click="menosHijo()"> Menos centros </PrimaryButton>-->
<!--                </div>-->
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="create">
                        {{ lang().button.add }} {{ form.processing ?  '...' : '' }}
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
.muted {
    color: #1b416699;
}

[name="labelSelectVue"] {
    /* font-size: 22px; */
    font-weight: 600;
}
</style>
