<?php

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

namespace App\Http\Controllers\Tenants;

use AdvisingApp\Ai\Actions\SyncTenantSmartPrompts;
use App\DataTransferObjects\LicenseManagement\LicenseAddonsData;
use App\DataTransferObjects\LicenseManagement\LicenseData;
use App\DataTransferObjects\LicenseManagement\LicenseLimitsData;
use App\DataTransferObjects\LicenseManagement\LicenseSubscriptionData;
use App\Enums\SubscriptionStatus;
use App\Features\SubscriptionExpirationFeature;
use App\Http\Requests\Tenants\SyncTenantRequest;
use App\Jobs\UpdateTenantLicenseData;
use App\Models\Tenant;
use App\Settings\TenantExpirationSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncTenantController
{
    public function __invoke(SyncTenantRequest $request, Tenant $tenant): JsonResponse
    {
        $licenseData = new LicenseData(
            updatedAt: now(),
            subscription: LicenseSubscriptionData::from($request->validated('subscription')),
            limits: LicenseLimitsData::from($request->validated('limits')),
            addons: LicenseAddonsData::from($request->validated('addons')),
        );

        try {
            dispatch_sync(new UpdateTenantLicenseData($tenant, $licenseData));

            if (SubscriptionExpirationFeature::active()) {
                // Subscription status and the expiration banner both live in the landlord
                // database, so they are committed together on the landlord connection.
                DB::connection('landlord')->transaction(function () use ($request, $tenant): void {
                    if (filled($subscriptionStatus = $request->validated('subscriptionStatus'))) {
                        $tenant->subscription_status = SubscriptionStatus::from($subscriptionStatus);
                        $tenant->save();
                    }

                    if (filled($bannerText = $request->validated('expirationBannerText'))) {
                        $settings = app(TenantExpirationSettings::class);
                        $settings->period_2_banner_text = $bannerText;
                        $settings->save();
                    }
                });
            }

            $tenant->execute(function () use ($request): void {
                DB::connection('tenant')->transaction(function () use ($request): void {
                    app(SyncTenantSmartPrompts::class)->execute($request);
                });
            });
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Failed to sync tenant.'], 500);
        }

        return response()->json();
    }
}
