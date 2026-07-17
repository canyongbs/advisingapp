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
import axios from '../Globals/Axios.js';
import { configureApi } from '../Services/api.js';
import determineIfUserIsAuthenticated from '../Services/DetermineIfUserIsAuthenticated.js';
import { useAuthStore } from '../Stores/auth.js';
import { useConfigStore } from '../Stores/config.js';
import { useTokenStore } from '../Stores/token.js';

/**
 * Boots the portal once, before any route navigation resolves.
 *
 * Fetches the portal configuration, hydrates the config / auth stores, configures
 * the API client used by the data loaders, and determines whether the current user
 * is authenticated. Route data loaders are gated on the returned promise so they
 * always run with a resolved API base URL and auth state.
 *
 * This never rejects; failures are surfaced via `config.errorLoading` so the shell
 * can render an error state instead of hanging the navigation.
 */
export async function bootPortal(props, pinia) {
    const config = useConfigStore(pinia);
    const auth = useAuthStore(pinia);
    const token = useTokenStore(pinia);

    configureApi({ getToken: () => token.getToken() });

    try {
        const { data } = await axios.get(props.url);

        config.applyResponse(data, props);
        configureApi({ baseUrl: config.apiUrl });

        await auth.setRequiresAuthentication(data.requires_authentication);

        if (config.userAuthenticationUrl) {
            auth.setUserIsAuthenticated(await determineIfUserIsAuthenticated(config.userAuthenticationUrl));
        }
    } catch (error) {
        config.errorLoading = true;
        console.error(`Resource Hub Embed ${error}`);
    } finally {
        config.isReady = true;
    }
}
