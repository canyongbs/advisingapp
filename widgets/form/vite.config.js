import { resolve } from 'path';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [vue()],
    build: {
        lib: {
            // Could also be a dictionary or array of multiple entry points
            entry: resolve(__dirname, 'src/widget.js'),
            name: 'FormWidget',
            // the proper extensions will be added
            fileName: 'form-widget',
        },
        outDir: resolve(__dirname, '../../public/js/widgets/form'),
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
        },
    },
    define: { 'process.env.NODE_ENV': '"production"' },
});
