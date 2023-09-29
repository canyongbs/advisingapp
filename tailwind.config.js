import preset from './vendor/filament/support/tailwind.config.preset';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

export default {
    presets: [preset],
    safelist: ['text-black'],
    content: [
        './app/Filament/**/*.php',
        './app-modules/**/src/Filament/**/*.php',
        './app-modules/**/resources/views/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                gray: require('tailwindcss/colors').zinc,
            },
            maxHeight: {
                content: 'calc(100vh - 6rem)',
            },
        },
    },
    plugins: [forms, typography, require('flowbite/plugin')],
};
