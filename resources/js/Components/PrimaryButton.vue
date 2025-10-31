<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type"
        class="inline-flex items-center px-4 py-2 bg-blue-600
         dark:bg-blue-600 border border-transparent rounded-lg font-semibold text-xs
          text-white dark:text-white uppercase tracking-widest hover:bg-blue-600/80 dark:hover:bg-blue-600/90
           focus:bg-blue-600/80 dark:focus:bg-blue-600/80 active:bg-blue-600 dark:active:bg-blue-600/80 
           focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 dark:focus:ring-offset-blue-600
            transition ease-in-out duration-700 disabled:bg-blue-600/80">
        <slot />
    </button>
</template>
