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

namespace App\Jobs;

use App\Models\Scopes\ExcludeExpiredSubscriptions;
use App\Models\Scopes\SetupIsComplete;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

abstract class DispatchForEachTenant implements ShouldBeUnique, ShouldQueue, NotTenantAware
{
    use Queueable;

    /**
     * Safety ceiling for the uniqueness lock; the lock is released on completion, this only
     * bounds recovery if a run dies mid-flight.
     */
    public int $uniqueFor = 300;

    public function __construct()
    {
        $this->onQueue(config('queue.landlord_queue'));
    }

    public function handle(): void
    {
        Tenant::query()
            ->tap(new SetupIsComplete())
            ->tap(new ExcludeExpiredSubscriptions())
            ->cursor()
            ->each(function (Tenant $tenant): void {
                try {
                    $tenant->execute(function () use ($tenant): void {
                        $job = $this->jobForTenant($tenant);

                        if ($job === null) {
                            return;
                        }

                        // Statement, never returned: the PendingDispatch must push while the tenant is still current.
                        dispatch($job);
                    });
                } catch (Throwable $throw) {
                    report($throw);
                }
            });
    }

    abstract protected function jobForTenant(Tenant $tenant): ?object;
}
