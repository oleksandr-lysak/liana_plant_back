import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    process.env.NODE_ENV = mode;


    return {
        base: '/build/',
        server: {
            https: false,
            host: true,
        },
        plugins: [
            laravel({
                input: [
                    'resources/js/app.ts',
                    'resources/css/app.css',
                ],
                ssr: 'resources/js/ssr.ts',
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
        ],
        define: {
            'process.env.NODE_ENV': JSON.stringify(mode),
        },
    };
});
