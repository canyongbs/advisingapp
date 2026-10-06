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

namespace App\Actions\QueueMonitoring;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Spatie\Multitenancy\Landlord;

class RetryFailedJobs
{
    /**
     * Retries failed jobs from the given tenant's failed_jobs table, or the landlord's when no tenant is given.
     * `queue:retry` makes each job's own tenant current while pushing it, which keeps it in that tenant's SQS message
     * group.
     *
     * @param  list<string>  $failedJobIds
     */
    public function __invoke(array $failedJobIds, ?Tenant $tenant = null): void
    {
        $retry = fn (): int => Artisan::call('queue:retry', ['id' => $failedJobIds]);

        if ($tenant === null) {
            Landlord::execute($retry);

            return;
        }

        $tenant->execute($retry);
    }
}
