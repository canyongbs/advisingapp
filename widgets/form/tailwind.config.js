import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import FormKitVariants from '@formkit/themes/tailwindcss';

export default {
    content: ['./src/**/*.vue', './src/tailwind-theme.js'],
    // theme: theme,
    plugins: [forms, typography, FormKitVariants],
};
