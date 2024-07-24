/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import defaultTheme from 'tailwindcss/defaultTheme'

const keys = [
    'primary',
    'danger',
    'warning',
    'success',
    'info',
    'gray',
]

const buildColors = () => {
    const colors = {}
    keys.forEach((key) => {
        colors[key] = {
            50: `rgba(var(--${key}-50), <alpha-value>)`,
            100: `rgba(var(--${key}-100), <alpha-value>)`,
            200: `rgba(var(--${key}-200), <alpha-value>)`,
            300: `rgba(var(--${key}-300), <alpha-value>)`,
            400: `rgba(var(--${key}-400), <alpha-value>)`,
            500: `rgba(var(--${key}-500), <alpha-value>)`,
            600: `rgba(var(--${key}-600), <alpha-value>)`,
            700: `rgba(var(--${key}-700), <alpha-value>)`,
            800: `rgba(var(--${key}-800), <alpha-value>)`,
            900: `rgba(var(--${key}-900), <alpha-value>)`,
            950: `rgba(var(--${key}-950), <alpha-value>)`,
        }
    })
    return colors
}

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './src/**/*.php'
    ],
    theme: {
        extend: {
            colors: {
                custom: {
                    50: 'rgba(var(--c-50), <alpha-value>)',
                    100: 'rgba(var(--c-100), <alpha-value>)',
                    200: 'rgba(var(--c-200), <alpha-value>)',
                    300: 'rgba(var(--c-300), <alpha-value>)',
                    400: 'rgba(var(--c-400), <alpha-value>)',
                    500: 'rgba(var(--c-500), <alpha-value>)',
                    600: 'rgba(var(--c-600), <alpha-value>)',
                    700: 'rgba(var(--c-700), <alpha-value>)',
                    800: 'rgba(var(--c-800), <alpha-value>)',
                    900: 'rgba(var(--c-900), <alpha-value>)',
                    950: 'rgba(var(--c-950), <alpha-value>)',
                },
                ...buildColors(),
            },
            fontFamily: {
                sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms, typography],
}
