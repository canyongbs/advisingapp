import preset from '../../vendor/filament/support/tailwind.config.preset';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import theme from '../../tailwind.config.js';

export default {
    presets: [preset],
    content: ['./widget/src/**/*.vue'],
    theme: theme,
    plugins: [forms, typography, require('flowbite/plugin')],
};
