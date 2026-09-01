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

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use Illuminate\Contracts\Console\Kernel;

describe('schedule', function () {
    it('does not schedule tenant tasks for tenants with an expired subscription', function () {
        $activeTenant = Tenant::factory()->create([
            'domain' => 'active-subscription.advisingapp.local',
            'setup_complete' => true,
            'subscription_status' => SubscriptionStatus::Active,
        ]);

        $expiredTenant = Tenant::factory()->create([
            'domain' => 'expired-subscription.advisingapp.local',
            'setup_complete' => true,
            'subscription_status' => SubscriptionStatus::Expired,
        ]);

        $summaries = collect(app(Kernel::class)->resolveConsoleSchedule()->events())
            ->map(fn ($event) => $event->getSummaryForDisplay());

        expect($summaries->contains(fn (string $summary) => str_contains($summary, $activeTenant->domain)))->toBeTrue()
            ->and($summaries->contains(fn (string $summary) => str_contains($summary, $expiredTenant->domain)))->toBeFalse();

        // Prevents the shared test tenant teardown from resolving one of these non-migratable tenants via Tenant::firstOrFail().
        $activeTenant->delete();
        $expiredTenant->delete();
    });
});
