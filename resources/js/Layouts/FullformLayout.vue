<script setup>
import { ref } from 'vue';
import NavbarVue from '@/Components/Navbar.vue';
import SideBarVue from "@/Components/SideBar.vue";
import Toast from '@/Components/Toast.vue';
import Footer from '@/Components/Footer.vue';

const sidebarOpened = ref(false);
const emit = defineEmits(['close', 'open']);


const categorias = ref([
  { nombre: 'Opcion 1', tiempo: '23h : 57m', activo: true },
  { nombre: 'Opcion 2', activo: false },
  { nombre: 'Opcion 3', activo: false },
])

function seleccionar(item) {
  categorias.value.forEach(i => i.activo = false)
  item.activo = true
}
</script>

<template>
  <div class="flex flex-col w-80 h-screen bg-gradient-to-b from-gray-800 to-gray-900 text-white shadow-lg border-r border-gray-700">
    <!-- Botón Atrás -->
    <div class="flex items-center p-4 border-b border-gray-700">
      <button
        @click="$emit('back')"
        class="flex items-center space-x-2 text-gray-300 hover:text-white transition"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="font-semibold text-sm">Atrás</span>
      </button>
    </div>

    <!-- Categorías -->
    <div class="flex flex-col mt-4 space-y-2 px-4">
      <button
        v-for="(item, index) in categorias"
        :key="index"
        @click="seleccionar(item)"
        class="relative flex flex-col items-start p-4 rounded-md border border-gray-700 hover:bg-gray-700/50 transition"
        :class="{'border-yellow-400 bg-gray-700/40': item.activo}"
      >
        <div class="flex justify-between w-full items-center">
          <span class="font-semibold text-base">{{ item.nombre }}</span>
          <span v-if="item.tiempo" class="text-yellow-400 font-mono text-sm">{{ item.tiempo }}</span>
        </div>
      </button>
    </div>
  </div>
</template>
<style scoped></style>
