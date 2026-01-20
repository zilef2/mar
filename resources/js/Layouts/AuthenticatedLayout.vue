<script setup>
import { ref } from 'vue';
import NavbarVue from '@/Components/Navbar.vue';
import SideBarVue from "@/Components/SideBar.vue";
import Toast from '@/Components/Toast.vue';
import Footer from '@/Components/Footer.vue';

const sidebarOpened = ref(false);
const emit = defineEmits(['close', 'open']);
</script>

<template>
    <div class="flex w-full overflow-hidden">
        <SideBarVue :open="sidebarOpened" @close="sidebarOpened = false" />
        <div
            class="w-full min-h-screen block bg-gray-100 dark:bg-gray-900 transition-all duration-300"
            :class="sidebarOpened ? 'lg:pl-64' : 'pl-0'"
        >
            <Toast :flash="$page.props.flash" />
            <NavbarVue :open="sidebarOpened" @open="sidebarOpened = !sidebarOpened" />
            <main class="max-w-8xl mx-auto sm:px-6 lg:px-8 pb-10 text-gray-900 dark:text-gray-100 dark:bg-gray-900 text-md">
                <slot />
            </main>
<!--            <Footer />-->
        </div>
    </div>
</template>
