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
        // Minification & optimization
        build: {
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
        
        // Source maps only for development
        sourcemap: process.env.NODE_ENV === 'development',
        
        // CSS code splitting
        cssCodeSplit: true,
        
        // Chunk size warning limit (increase for large projects)
        chunkSizeWarningLimit: 1000,
        
        // Output directory
        outDir: 'public/build',
        
        // Manual chunks for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['axios'],
                    'tailwind': ['tailwindcss'],
                }
            }
        }
    },
    
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        // HMR configuration for development
        hmr: {
            host: 'localhost',
            port: 5173,
        }
    },
    
    // Optimization
    optimizeDeps: {
        include: ['axios'],
    }
});
