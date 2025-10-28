const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/vue-select/dist/vue-select.css',
        './node_modules/@vuepic/vue-datepicker/dist/main.css'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "rgb(var(--color-primary) / <alpha-value>)",
            },
            width: {
                '1.5': '0.375rem',
            },
            height: {
                '1.5': '0.375rem',
            }
        },
    },

    plugins: [
        require("@tailwindcss/forms")({
            strategy: 'base', // only generate global styles
          }),
    ],
};
