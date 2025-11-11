<script setup>
import { computed } from 'vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ArrowTrendingDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    data: { type: Object, required: true },
    titulos: { type: Array, required: true },
    fromController: { type: Object, required: true },
    can: { type: Function, required: true },
    lang: { type: Function, required: true },
    number_format: { type: Function, required: true },
    formatDate: { type: Function, required: true },
    TimeTo12Format: { type: Function, required: true },
})
</script>

<template>
    <tbody v-if="data.hayCongelado !== 0" v-sticky="{ zIndex: 100 }"
           class="w-full bg-white border border-blue-500 inamovible">

        <!-- Encabezado congelado -->
        <tr class="dark:bg-gray-900/50 text-left">
            <th class="px-2 py-4"></th>
            <th class="px-2 py-4"></th>

            <th class="px-2 py-4 text-center">
                <PrimaryButton
                    @click="data.hayCongelado = 0"
                    v-show="data.hayCongelado && can(['delete reporte'])"
                    class="px-3 py-1.5"
                    v-tooltip="'Descongelar'"
                >
                    <ArrowTrendingDownIcon class="w-5 h-5" />
                </PrimaryButton>
            </th>

            <th v-for="titulo in titulos" :key="titulo.order" class="px-2 py-4 cursor-pointer">
                <div class="flex justify-between items-center">
                    <p class="w-20 text-sm">{{ lang().label[titulo.label] }}</p>
                </div>
            </th>
        </tr>

        <!-- Fila congelada con datos -->
        <tr>
            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"></td>
            <td class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"></td>
            <td class="whitespace-nowrap py-4 px-2 sm:py-3"></td>
            <td class="whitespace-nowrap py-4 px-2 sm:py-3"></td>

            <td v-for="titulo in titulos" :key="titulo.order"
                class="whitespace-nowrap py-4 px-2 sm:py-3">

                <!-- Tipos de celda -->
                <span v-if="titulo.type === 'text'">
                    {{ fromController.data[0][titulo.order] }}
                </span>

                <span v-else-if="titulo.type === 'time'">
                    {{ TimeTo12Format(fromController.data[0][titulo.order]) }}
                </span>

                <span v-else-if="titulo.type === 'number'">
                    {{ number_format(fromController.data[0][titulo.order], 0, false) }}
                </span>

                <span v-else-if="titulo.type === 'dinero'">
                    {{ number_format(fromController.data[0][titulo.order], 0, true) }}
                </span>

                <span v-else-if="titulo.type === 'date'">
                    {{ formatDate(fromController.data[0][titulo.order], '') }}
                </span>

                <span v-else-if="titulo.type === 'datetime'">
                    {{ formatDate(fromController.data[0][titulo.order], 'conLaHora') }}
                </span>

                <span v-else-if="titulo.type === 'foreign'">
                    {{ fromController.data[0][titulo.nameid] }}
                </span>

                <span v-if="titulo.order === 'hora_final' && fromController.data[0][titulo.order] == null">-</span>

                <span
                    v-if="titulo.type === 'decimal' && titulo.order === 'tiempo_transcurrido'"
                >
                    {{
                        fromController.data[0][titulo.order] > 60
                            ? number_format(fromController.data[0][titulo.order] * 60, 3, false) + 'mins'
                            : number_format(fromController.data[0][titulo.order], 3, false)
                    }}
                </span>

                <span v-else-if="titulo.type === 'decimal'">
                    {{ number_format(fromController.data[0][titulo.order], 3, false) }}
                </span>
            </td>
        </tr>
    </tbody>
</template>
