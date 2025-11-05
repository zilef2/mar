<script setup>
import vSelect from "vue-select";
import InputError from "@/Components/InputError.vue";
import { computed } from "vue";

// Props que controlan el nombre base y los datos del formulario
const props = defineProps({
  name: { type: String, required: true }, // Ej: "centrotrabajo" o "ciudad"
  form: { type: Object, required: true },
  data: { type: Object, required: true },
});

// key completo: ej. "centrotrabajo_id"
const field = computed(() => {
    // let thename = props.name.replace(/_/g, '') // reemplaza todos los _ por espacios
    // return `${thename}_id`
    return 'or'
});

// Capitaliza la primera letra para usar en el label
const labelText = computed(() => {
  return props.name
    .replace(/_/g, ' ')
    .toLowerCase()
    .replace(/\b\w/g, c => c.toUpperCase());
});

</script>

<template>
  <div :id="`S${props.name}`" class="xs:col-span-1 md:col-span-2">
    <label :name="field" class="dark:text-white">{{ labelText }}</label>

    <vSelect
      :options="data[field]"
      label="title"
      class="dark:bg-gray-400"
      v-model="form[field]"
      append-to-body
    />

    <InputError class="mt-2" :message="form.errors[field]" />
  </div>
</template>
