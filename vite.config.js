import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    build: {
        minify: 'esbuild',
        sourcemap: process.env.NODE_ENV === 'development',
        cssCodeSplit: true,
        chunkSizeWarningLimit: 1000,
        outDir: 'public/build',

        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios'],
                    tailwind: ['tailwindcss'],
                },
            },
        },
    },

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },

    optimizeDeps: {
        include: ['axios'],
    },
});