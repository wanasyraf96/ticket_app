import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: [ 'resources/css/app.css', 'resources/js/app.js' ],
            refresh: true,
        }),
        vue({
            refresh: true
        }),
    ],
    server: {
        hmr: 'localhost',
        watch: {
            usePolling: true
        }
    },
    resolve: {
        alias: {
            '@': './resources/js/',
            'vue': 'vue/dist/vue.esm-bundler',
        }
    }
});
