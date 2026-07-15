/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
import { storeToRefs } from 'pinia';
import { computed } from 'vue';
import { useConfigStore } from '../Stores/config.js';

/**
 * Computes a perceptually-correct contrast colour for text placed on top of
 * a given sRGB background expressed as "R G B" space-separated integers.
 * Returns '#111827' (near-black) for light palettes (yellow, lime, amber, etc.)
 * and 'white' for dark palettes, so primary-variant button text is always readable
 * regardless of which static primary colour the admin has configured.
 */
function contrastOnColor(rgbString) {
    const parts = String(rgbString ?? '')
        .trim()
        .split(/\s+/)
        .map(Number);

    if (parts.length !== 3 || parts.some((n) => isNaN(n))) {
        return 'white';
    }

    const [r, g, b] = parts.map((c) => {
        const s = c / 255;

        return s <= 0.04045 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
    });

    const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;

    return luminance > 0.35 ? '#111827' : 'white';
}

/**
 * Exposes the reactive CSS custom property map that themes the portal (primary
 * palette, contrast-safe text colour and rounding scale).
 */
export function usePortalTheme() {
    const config = useConfigStore();
    const { primaryColor, rounding } = storeToRefs(config);

    const primaryOnColor = computed(() => contrastOnColor(primaryColor.value?.[500]));

    const themeStyles = computed(() => {
        const palette = primaryColor.value || {};

        return {
            '--primary-50': palette[50],
            '--primary-100': palette[100],
            '--primary-200': palette[200],
            '--primary-300': palette[300],
            '--primary-400': palette[400],
            '--primary-500': palette[500],
            '--primary-600': palette[600],
            '--primary-700': palette[700],
            '--primary-800': palette[800],
            '--primary-900': palette[900],
            '--primary-950': palette[950],
            '--primary-on-color': primaryOnColor.value,
            '--rounding-sm': rounding.value.sm,
            '--rounding': rounding.value.default,
            '--rounding-md': rounding.value.md,
            '--rounding-lg': rounding.value.lg,
            '--rounding-full': rounding.value.full,
        };
    });

    return { themeStyles, primaryOnColor };
}
