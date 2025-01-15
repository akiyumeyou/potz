import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/senryu.js',
                'resources/css/senryu.css',
                'resources/js/thank.js',
            ],
            refresh: true,
        }),
    ],
});
