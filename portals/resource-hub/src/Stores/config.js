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
import { defineStore } from 'pinia';
import { ref } from 'vue';

const roundingScales = {
    none: { sm: '0px', default: '0px', md: '0px', lg: '0px', full: '0px' },
    sm: { sm: '0.125rem', default: '0.25rem', md: '0.375rem', lg: '0.5rem', full: '9999px' },
    md: { sm: '0.25rem', default: '0.375rem', md: '0.5rem', lg: '0.75rem', full: '9999px' },
    lg: { sm: '0.375rem', default: '0.5rem', md: '0.75rem', lg: '1rem', full: '9999px' },
    full: { sm: '9999px', default: '9999px', md: '9999px', lg: '9999px', full: '9999px' },
};

export const useConfigStore = defineStore('config', () => {
    const isReady = ref(false);
    const errorLoading = ref(false);

    const searchUrl = ref(null);
    const apiUrl = ref(null);
    const appUrl = ref(null);
    const userAuthenticationUrl = ref(null);

    const appName = ref('');
    const headerLogo = ref('');
    const footerLogo = ref('');

    const primaryColor = ref('');
    const rounding = ref(roundingScales.md);

    const authenticationRequestUrl = ref(null);

    function applyResponse(data, props) {
        searchUrl.value = data.search_url ?? props.searchUrl ?? searchUrl.value;
        apiUrl.value = data.api_url ?? props.apiUrl ?? apiUrl.value;
        userAuthenticationUrl.value = data.user_authentication_url ?? props.userAuthenticationUrl ?? userAuthenticationUrl.value;
        appUrl.value = data.app_url ?? props.appUrl ?? appUrl.value;

        appName.value = data.app_name ?? '';
        headerLogo.value = data.header_logo ?? '';
        footerLogo.value = data.footer_logo ?? '';

        primaryColor.value = data.primary_color ?? '';
        rounding.value = roundingScales[data.rounding ?? 'md'] ?? roundingScales.md;

        authenticationRequestUrl.value = data.authentication_url ?? null;
    }

    return {
        isReady,
        errorLoading,
        searchUrl,
        apiUrl,
        appUrl,
        userAuthenticationUrl,
        appName,
        headerLogo,
        footerLogo,
        primaryColor,
        rounding,
        authenticationRequestUrl,
        applyResponse,
    };
});
