import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    server: {
        host: 'mar.t', // Usa tu dominio virtual
        hmr: {
            host: 'mar.t',
            protocol: 'wss', // Forzar protocolo WebSocket Secure
            port: 5173, // Puerto predeterminado de Vite
        },
    },
    // ********************************
});
