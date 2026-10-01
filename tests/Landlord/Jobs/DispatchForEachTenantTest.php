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
use App\Jobs\DispatchForEachTenant;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Queue;

use function Sentry\configureScope;

use Sentry\Event;
use Sentry\State\Scope;

it('dispatches its job for each eligible tenant', function () {
    $dispatcher = new class () extends DispatchForEachTenant {
        protected function jobForTenant(Tenant $tenant): object
        {
            return new class () implements ShouldQueue {
                use Queueable;

                public function handle(): void {}
            };
        }
    };

    Queue::fake();

    $dispatcher->handle();

    Queue::assertCount(1);
});

it('does not dispatch for ineligible tenants', function () {
    $expiredTenant = Tenant::factory()->create([
        'domain' => 'expired-subscription.advisingapp.local',
        'setup_complete' => true,
        'subscription_status' => SubscriptionStatus::Expired,
    ]);

    $incompleteTenant = Tenant::factory()->create([
        'domain' => 'setup-incomplete.advisingapp.local',
        'setup_complete' => false,
        'subscription_status' => SubscriptionStatus::Active,
    ]);

    $dispatcher = new class () extends DispatchForEachTenant {
        protected function jobForTenant(Tenant $tenant): object
        {
            return new class () implements ShouldQueue {
                use Queueable;

                public function handle(): void {}
            };
        }
    };

    Queue::fake();

    $dispatcher->handle();

    // Only the single eligible provisioned tenant is dispatched for.
    Queue::assertCount(1);

    // Prevents the shared test tenant teardown from resolving these non-migratable tenants via Tenant::firstOrFail().
    $expiredTenant->delete();
    $incompleteTenant->delete();
});

it('does not dispatch when the job for a tenant resolves to null', function () {
    $dispatcher = new class () extends DispatchForEachTenant {
        protected function jobForTenant(Tenant $tenant): ?object
        {
            return null;
        }
    };

    Queue::fake();

    $dispatcher->handle();

    Queue::assertNothingPushed();
});

it('reports the failure and continues dispatching for the remaining tenants', function () {
    $secondEligibleTenant = Tenant::factory()->create([
        'domain' => 'second-eligible.advisingapp.local',
        'setup_complete' => true,
        'subscription_status' => SubscriptionStatus::Active,
    ]);

    $dispatcher = new class () extends DispatchForEachTenant {
        private bool $hasThrown = false;

        protected function jobForTenant(Tenant $tenant): object
        {
            // Fail the first tenant so a dispatched job for a later tenant proves the loop continued.
            if (! $this->hasThrown) {
                $this->hasThrown = true;

                throw new RuntimeException('Boom');
            }

            return new class () implements ShouldQueue {
                use Queueable;

                public function handle(): void {}
            };
        }
    };

    Exceptions::fake();
    Queue::fake();

    $dispatcher->handle();

    Exceptions::assertReported(fn (RuntimeException $throw) => $throw->getMessage() === 'Boom');

    Queue::assertCount(1);

    // Prevents the shared test tenant teardown from resolving this non-migratable tenant via Tenant::firstOrFail().
    $secondEligibleTenant->delete();
});

it('tags the failure report with the tenant it failed for', function () {
    $tenant = Tenant::query()->firstOrFail();

    $reportedTags = null;

    Exceptions::reportable(function (RuntimeException $throw) use (&$reportedTags): void {
        configureScope(function (Scope $scope) use (&$reportedTags): void {
            $reportedTags = $scope->applyToEvent(Event::createEvent())?->getTags();
        });
    })->stop();

    $dispatcher = new class () extends DispatchForEachTenant {
        protected function jobForTenant(Tenant $tenant): object
        {
            throw new RuntimeException('Boom');
        }
    };

    Queue::fake();

    $dispatcher->handle();

    expect($reportedTags)->toMatchArray([
        'tenant.id' => $tenant->getKey(),
        'tenant.name' => $tenant->name,
    ]);
});
