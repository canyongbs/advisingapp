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

use AdvisingApp\Ai\Actions\SyncTenantSmartPrompts;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Tenants\SyncTenantController;
use App\Http\Requests\Tenants\SyncTenantRequest;
use App\Jobs\UpdateTenantLicenseData;
use App\Models\Tenant;
use App\Settings\TenantExpirationSettings;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;

function syncTenantControllerRequest(): SyncTenantRequest
{
    $request = SyncTenantRequest::create('/', 'POST', [
        'limits' => [
            'conversationalAiSeats' => 1,
            'retentionCrmSeats' => 1,
            'recruitmentCrmSeats' => 1,
            'emails' => 1,
            'sms' => 1,
            'dataAdvisorsCount' => 1,
            'resetDate' => '01-01',
            'employeeAdvisorsCount' => 1,
            'customerAdvisorsCount' => 1,
        ],
        'addons' => [
            'employeeAdvisors' => false,
            'customerAdvisors' => false,
            'onlineForms' => false,
            'onlineSurveys' => false,
            'onlineAdmissions' => false,
            'resourceHub' => false,
            'supportPrograms' => false,
            'eventManagement' => false,
            'realtimeChat' => false,
            'mobileApps' => false,
            'scheduleAndAppointments' => false,
            'researchAdvisor' => false,
            'dataAdvisor' => false,
            'earlyAlert' => false,
            'publicProfiles' => false,
        ],
        'subscription' => [
            'clientName' => 'Updated Client',
            'partnerName' => 'Updated Partner',
            'startDate' => now()->subYear()->toIso8601String(),
            'endDate' => now()->addYear()->toIso8601String(),
        ],
        'subscriptionStatus' => SubscriptionStatus::Expired->value,
        'expirationBannerText' => 'Updated expiration banner.',
    ]);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->validateResolved();

    return $request;
}

it('does not persist other sync changes when smart prompt validation fails', function () {
    Bus::fake();

    $tenant = Tenant::query()->firstOrFail();
    $tenant->subscription_status = SubscriptionStatus::Active;
    $tenant->save();

    $originalBannerText = app(TenantExpirationSettings::class)->period_2_banner_text;

    $syncTenantSmartPrompts = Mockery::mock(SyncTenantSmartPrompts::class);
    $syncTenantSmartPrompts
        ->shouldReceive('execute')
        ->once()
        ->andThrow(ValidationException::withMessages([
            'smartPrompts.0.smart_prompts.0.title' => 'The smart prompt title conflicts with an existing custom prompt.',
        ]));
    assert($syncTenantSmartPrompts instanceof SyncTenantSmartPrompts);

    expect(fn () => app(SyncTenantController::class)(
        syncTenantControllerRequest(),
        $tenant,
        $syncTenantSmartPrompts,
    ))->toThrow(ValidationException::class);

    Bus::assertNotDispatched(UpdateTenantLicenseData::class);

    expect($tenant->refresh()->subscription_status)->toBe(SubscriptionStatus::Active)
        ->and(app(TenantExpirationSettings::class)->period_2_banner_text)->toBe($originalBannerText);
});
