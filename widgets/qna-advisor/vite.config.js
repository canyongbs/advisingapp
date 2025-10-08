/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [vue()],
    experimental: {
        renderBuiltUrl(filename) {
            return {
                runtime: `window.__VITE_QNA_ADVISOR_RESOURCE_URL__ + ${JSON.stringify(filename)}`,
            };
        },
    },
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                widget: resolve(__dirname, './src/widget.js'),
                loader: resolve(__dirname, './src/loader.js'),
            },
            output: {
                entryFileNames: (chunkInfo) => {
                    return chunkInfo.name === 'loader'
                        ? 'advising-app-qna-advisor-widget.js'
                        : 'advising-app-qna-advisor-widget-app-[hash].js';
                },
                assetFileNames: (assetInfo) => {
                    if (assetInfo.names?.[0]?.endsWith('.css')) {
                        return 'advising-app-qna-advisor-widget-[hash].css';
                    }
                    return '[name]-[hash][extname]';
                },
                // Place chunks directly in the root
                chunkFileNames: '[name]-[hash].js',
            },
        },
        outDir: resolve(__dirname, '../../public/js/widgets/qna-advisor'),
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
