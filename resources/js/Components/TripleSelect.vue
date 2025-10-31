<template>
  <div class="grid gap-3">
    <vSelect
      :options="opcionesCentro"
      label="label"
      v-model="form.centro"
      @update:modelValue="onSelect('centro', $event)"
      placeholder="Selecciona un centro"
    />

    <vSelect
      :options="opcionesActividad(form.centro?.value)"
      label="label"
      v-model="form.actividad"
      @update:modelValue="onSelect('actividad', $event)"
      placeholder="Selecciona una actividad"
      :disabled="!form.centro"
    />

    <vSelect
      :options="opcionesReporte(form.actividad?.value)"
      label="label"
      v-model="form.reporte"
      @update:modelValue="onSelect('reporte', $event)"
      placeholder="Selecciona un reporte"
      :disabled="!form.actividad"
    />
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import vSelect from "vue-select";
import { useForm, usePage } from '@inertiajs/vue3';


const props = defineProps({
    vectorTriple
})

const form = useForm({
  centro: null,
  actividad: null,
  reporte: null,
  seleccionables: []
})

const opcionesCentro = computed(() => props.vectorTriple)

const opcionesActividad = computed(() => {
  const centro = props.vectorTriple.find(c => c.value === form.centro?.value)
  return centro ? centro.actividades : []
})

const opcionesReporte = computed(() => {
  const centro = props.vectorTriple.find(c => c.value === form.centro?.value)
  const actividad = centro?.actividades?.find(a => a.value === form.actividad?.value)
  return actividad ? actividad.reportes : []
})


function onSelect(tipo, seleccion) {
  const mapping = { centro: 0, actividad: 1, reporte: 2 }
  form.seleccionables[mapping[tipo]] = {
    value: seleccion?.value,
    label: seleccion?.label
  }
  // Limpia dependencias hacia abajo
  if (tipo === 'centro') {
    form.actividad = null
    form.reporte = null
    form.seleccionables.splice(1)
  }
  if (tipo === 'actividad') {
    form.reporte = null
    form.seleccionables.splice(2)
  }
}
</script>
