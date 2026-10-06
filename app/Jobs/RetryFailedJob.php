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

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Spatie\Multitenancy\Jobs\NotTenantAware;

/**
 * Runs `queue:retry` against the failed_jobs table of either the landlord or one tenant. The command is used as is
 * because multitenancy makes a retried job's tenant current until the command finishes, which puts the job back in
 * its tenant's SQS message group.
 */
class RetryFailedJob implements ShouldQueue, NotTenantAware
{
    use Queueable;

    /**
     * @param  list<string>  $failedJobIds
     */
    public function __construct(
        public ?string $tenantId,
        public array $failedJobIds,
    ) {
        $this->onQueue(config('queue.landlord_queue'));
    }

    public static function failedJobProvider(string $connection): DatabaseUuidFailedJobProvider
    {
        return new DatabaseUuidFailedJobProvider(app(DatabaseManager::class), $connection, Config::string('queue.failed.table'));
    }

    public function handle(): void
    {
        if ($this->tenantId === null) {
            $this->retry('landlord');

            return;
        }

        Tenant::query()->findOrFail($this->tenantId)->execute(
            fn () => $this->retry(Config::string('multitenancy.tenant_database_connection_name')),
        );
    }

    private function retry(string $connection): void
    {
        // The framework's failed job provider is a singleton bound to the connection current when it was resolved.
        app()->instance('queue.failer', self::failedJobProvider($connection));

        try {
            Artisan::call('queue:retry', ['id' => $this->failedJobIds]);
        } finally {
            app()->forgetInstance('queue.failer');
        }
    }
}
