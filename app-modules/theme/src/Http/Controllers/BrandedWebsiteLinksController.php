<?php

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

namespace AdvisingApp\Theme\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AdvisingApp\Theme\Settings\ThemeSettings;
use AdvisingApp\Theme\Http\Requests\BrandedWebsiteLinksRequest;

class BrandedWebsiteLinksController extends Controller
{
    public function __invoke(BrandedWebsiteLinksRequest $request): JsonResponse
    {
        $data = $request->validated();

        $tenant = Tenant::findOrFail($data['tenant_id']);

        $tenant->execute(function () use ($data) {
            $settings = app(ThemeSettings::class);
            $settings->is_support_url_enabled = $data['is_support_url_enabled'];
            $settings->support_url = $data['support_url'];
            $settings->is_recent_updates_url_enabled = $data['is_recent_updates_url_enabled'];
            $settings->recent_updates_url = $data['recent_updates_url'];
            $settings->is_custom_link_url_enabled = $data['is_custom_link_url_enabled'];
            $settings->custom_link_label = $data['custom_link_label'];
            $settings->custom_link_url = $data['custom_link_url'];
            $settings->tenant_id = $data['tenant_id'];
            $settings->save();
        });

        return response()->json([
            'message' => 'Theme updated successfully!',
        ]);
    }
}
