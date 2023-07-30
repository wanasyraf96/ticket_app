import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // '**/*.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        // hmr: {
        //     host: 'localhost',
        // },
        // watch: {
        //     usePolling: true
        // }
    },
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler',
        }
    },
    mode: 'development'
});
