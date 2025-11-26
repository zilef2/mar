<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';
import {watchEffect, reactive} from 'vue';
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

import {TimeTo12Format, formatTime, formatDate, TransformTdate} from '@/global.ts';

const props = defineProps({
    show: Boolean,
    title: String,
    generica: Object,
})

const emit = defineEmits(["close"]);
const data = reactive({
    justNames: [
        // 'codigo',
        'hora_final',
    ],
})

const form = useForm({...Object.fromEntries(data.justNames.map(field => [field, '']))});
const printForm = [
    // { idd: 'codigo', label: 'codigo', type: 'text', value: form.codigo , elif:null},
    {idd: 'hora_final', label: 'hora final', type: 'time', value: form.hora_final, elif: null},
];

watchEffect(() => {
    if (props.show) {
        form.errors = {}

        form.hora_final = formatTime()

        //# hora final como la hora del sistema
        const validDate = new Date()
        let hora = validDate.getHours();
        let hourAndtime = (hora < 10 ? '0' : '') + (hora) + ':' + (validDate.getMinutes() < 10 ? '0' : '') + validDate.getMinutes() + ':00';
        form.hora_final = hourAndtime


        // form.hora_final = props.generica?.hora_final
    }
})


const update = () => {
    form.put(route('reporte.update', props.generica?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close")
            form.reset()
        },
        onError: () => null,
        onFinish: () => null,
    })
}
</script>
<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ lang().label.finLaboral }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-1 md:mx-[100px] mt-8 gap-6">

                    <div v-for="(campo, i) in printForm" :key="i">
                        <div v-if="campo.type === 'time'" class="inline-flex w-full items-center space-x-6">
                            <InputLabel
                                :for="campo.label"
                                :value="lang().label[campo.label]"
                                class="text-center w-40"
                            />

                            <div class="flex-1">
                                <TextInput
                                    :id="campo.idd"
                                    :type="campo.type"
                                    class="w-full text-xl text-center"
                                    v-model="form[campo.idd]"
                                    :placeholder="campo.label"
                                    :error="form.errors[campo.idd]"
                                    required
                                    disabled
                                    step="3600"
                                />
                                <InputError
                                    :message="form.errors[campo.idd]"
                                    class="mt-1"
                                />
                            </div>
                        </div>
                        <div v-else class="">
                            <InputLabel
                                :for="campo.label" :value="lang().label[campo.label]"/>
                            <TextInput
                                :id="campo.idd" :type="campo.type" class="mt-1 block w-full"
                                v-model="form[campo.idd]" :placeholder="campo.label"
                                :error="form.errors[campo.idd]"/>
                            <InputError class="mt-2" :message="form.errors[campo.idd]"/>
                        </div>
                    </div>

                </div>
                <div class=" my-8 flex justify-end">
                    <SecondaryButton :disabled="form.processing" @click="emit('close')"> Cancelar
                    </SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                   @click="update">
                        {{ form.processing ? 'Cerrar Tarea' + '...' : 'Cerrar Tarea' }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
