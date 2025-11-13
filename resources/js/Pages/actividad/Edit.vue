<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {watchEffect, reactive, onMounted, watch} from 'vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import VueDatePicker from '@vuepic/vue-datepicker';
import {PlusCircleIcon, XCircleIcon} from "@heroicons/vue/24/solid";


const props = defineProps({
    show: Boolean,
    title: String,
    generica: Object,
    titulos: Object, //parametros de la clase principal
    losSelect: Object,

})

const emit = defineEmits(["close"]);
const data = reactive({})

//very usefull
const justNames = props.titulos.map(names => names['order'])
const form = useForm({
    ...Object.fromEntries(justNames.map(field => [field, ''])),

});

onMounted(() => {
});

const printForm = [];
props.titulos.forEach(names => {
    printForm.push({
        idd: names['order'], label: names['label'], type: names['type']
        , value: form[names['order']]
    })
});


watchEffect(() => {
    if (props.show) {
        form.errors = {}
        props.titulos.forEach(names => {
            form[names['order']] = props.generica[names['order']]
        });
    }
})
// watch(() => props.show, (newX) => {
//     if (newX) {
//     }
// })

let validar = () => {
    
    data.valido = form.nombre;
    // if (!form.otrocampo) {
    //     data.valido = false
    // }
}

const update = () => {
    validar()
    if (data.valido) {
        form.put(route('actividad.update', props.generica?.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("close")
                form.reset()
            },
            onError: () => null,
            onFinish: () => null,
        })
    } else {
        data.mensajeError = 'Hay elementos vacios'
    }
}
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')" :maxWidth="'lg'">
            <form class="px-6 pt-6" @submit.prevent="update">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-56">
                    <div class="">
                        <InputLabel for="nombre" :value="lang().label.nombre"/>
                        <TextInput id="nombre" type="text" class="mt-1 block w-full"
                                   v-model="form['nombre']" required :placeholder="nombre"
                                   :error="form.errors['nombre']"/>
                        <InputError class="mt-2" :message="form.errors['nombre']"/>
                    </div>
                    <!--                    <div >-->
                    <!--                        <InputLabel for="tipo" :value="lang().label.Tipo" />-->
                    <!--                        <vSelect :options="tiposOptions" label="title"-->
                    <!--                                  class="mt-1 dark:bg-gray-500 rounded-lg dark:text-gray-600"-->
                    <!--                                  v-model="form.tipo"></vSelect>-->
                    <!--                    </div>-->

                </div>
                <div class=" my-8 flex justify-end">
                    <p class="text-lg mx-8 text-red-500">{{ data.mensajeError }}</p>
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> {{
                            lang().button.close
                        }}
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="update">
                        {{ form.processing ? lang().button.save + '...' : lang().button.save }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>

<style>
@reference "../../../css/app.css";

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
