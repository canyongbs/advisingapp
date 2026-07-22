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
import { ref } from 'vue';
import axios from '../Globals/Axios.js';
import { apiPost } from '../Services/api.js';
import { useConfigStore } from '../Stores/config.js';
import { useTokenStore } from '../Stores/token.js';

export function usePortalAuth() {
    const config = useConfigStore();

    const authentication = ref({
        code: null,
        email: null,
        isRequested: false,
        requestedMessage: null,
        url: null,
    });

    function requestCode(formData, node, done) {
        axios
            .post(config.authenticationRequestUrl, {
                email: formData.email,
                isSpa: true,
            })
            .then((response) => {
                if (!response.data.authentication_url) {
                    node.setErrors([response.data.message]);

                    return;
                }

                authentication.value.isRequested = true;
                authentication.value.requestedMessage = response.data.message;
                authentication.value.url = response.data.authentication_url;
            })
            .catch((error) => {
                node.setErrors([], error.response.data.errors);
            })
            .finally(() => done());
    }

    function verifyCode(formData, node, done) {
        const { setToken } = useTokenStore();

        const data = {
            code: formData.code,
        };

        axios
            .post(authentication.value.url, data)
            .then((response) => {
                if (response.errors) {
                    node.setErrors([], response.errors);

                    return;
                }

                if (response.data.is_expired) {
                    node.setErrors(['The authentication code expires after 24 hours. Please authenticate again.']);

                    authentication.value.isRequested = false;
                    authentication.value.requestedMessage = null;
                    authentication.value.url = null;

                    return;
                }

                if (response.data.success === true) {
                    setToken(response.data.token).then(() => window.location.reload());
                }
            })
            .catch((error) => {
                node.setErrors([], error.response.data.errors);
            })
            .finally(() => done());
    }

    function authenticate(formData, node, done) {
        node.clearErrors();

        if (authentication.value.isRequested) {
            verifyCode(formData, node, done);

            return;
        }

        requestCode(formData, node, done);
    }

    function logout() {
        const { removeToken } = useTokenStore();

        apiPost('/authenticate/logout').then((data) => {
            if (!data.success) {
                return;
            }

            removeToken();
            window.location.href = data.redirect_url;
        });
    }

    return { authentication, authenticate, logout };
}
