import { resolve } from 'path';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [vue()],
    build: {
        manifest: true,
        lib: {
            entry: resolve(__dirname, 'src/widget.js'),
            name: 'AssistFormWidget',
            fileName: 'assist-form-widget',
            formats: ['es'],
        },
        outDir: resolve(__dirname, '../../public/js/widgets/form'),
        emptyOutDir: true,
        sourcemap: true,
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
        },
    },
    define: { 'process.env.NODE_ENV': '"production"' },
});
