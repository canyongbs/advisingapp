import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
// import livewire from '@defstudio/vite-livewire-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                // TODO: Will need this when we start using filament
                // 'resources/css/filament.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        }
        // livewire({
        //     refresh: ['resources/css/filament.css'],
        // }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    }
});
