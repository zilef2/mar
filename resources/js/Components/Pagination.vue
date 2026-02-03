<script setup>
import { router } from '@inertiajs/vue3';
import { pickBy } from "lodash";
import { reactive, watchEffect } from "vue";
import Icon from "@/Components/Icon.vue";

const props = defineProps({
    links: Object,
    filters: Object
})

const data = reactive({
    params: {
        search: props.filters?.search,
        search2: props.filters?.search2,
        search3: props.filters?.search3,
        search4: props.filters?.search4,
        search5: props.filters?.search5,
        search6: props.filters?.search6,
        search7: props.filters?.search7,
        search8: props.filters?.search8,
        field: props.filters?.field,
        order: props.filters?.order,
        perPage: props.filters?.perPage,
        searchdia: props.filters.searchdia,
        soloTiEstimado: props.filters.soloTiEstimado,
        ultimosmeses: props.filters.ultimosmeses,
        FiltroCentro: props.filters.FiltroCentro,
    },
})

const goto = (link) => {
    let params = pickBy(data.params);
    router.get(link, params, {
        replace: true,
        preserveState: true,
        preserveScroll: true,
    })
}

watchEffect(() => {
    data.params.search = props.filters?.search
    data.params.search2 = props.filters?.search2
    data.params.search3 = props.filters?.search3
    data.params.search4 = props.filters?.search4
    data.params.search5 = props.filters?.search5
    data.params.search6 = props.filters?.search6
    data.params.field = props.filters?.field
    data.params.order = props.filters?.order
    data.params.perPage = props.filters?.perPage
    data.params.searchdia = props.filters?.searchdia
    data.params.soloTiEstimado = props.filters?.soloTiEstimado
    data.params.ultimosmeses = props.filters?.ultimosmeses
    data.params.FiltroCentro = props.filters?.FiltroCentro
})

</script>
<template>
    <div class="ml-2 mx-2" v-if="links.data.length !== 0">
        {{ links.from }}-{{ links.to }} {{ lang().label.of }} {{ links.total }}
    </div>
    <div class="flex flex-col space-y-2 mx-auto p-6 text-lg" v-if="links.data.length === 0">
        <Icon :name="'nodata'" class="w-auto h-16" />
        <p>{{ lang().label.no_data }}</p>
    </div>
    <div v-if="links.links.length > 3">
        <ul
            class="hidden lg:flex justify-center items-center rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <li v-for="(link, index) in links.links" :key="index">
                <button v-on:click="goto(link.url)" class="px-4 py-2 hover:bg-blue-600 hover:text-white"
                    :class="{ 'bg-blue-600 text-white': link.active }" v-html="link.label"
                    :disabled="link.url == null"></button>
            </li>
        </ul>
        <!-- <ul class="flex justify-center items-center rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <li>
                <button v-on:click="goto(links.prev_page_url)" class="px-4 py-2" v-html="'&laquo;'"
                    :disabled="links.prev_page_url == null"></button>
            </li>
            <li>
                <p class="px-4 py-2 bg-blue-600 text-white" v-html="links.current_page"></p>
            </li>
            <li>
                <button v-on:click="goto(links.next_page_url)" class="px-4 py-2" v-html="'&raquo;'"
                    :disabled="links.next_page_url == null"></button>
            </li>
        </ul> -->
    </div>
</template>
