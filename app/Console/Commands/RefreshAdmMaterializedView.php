<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class RefreshAdmMaterializedView extends Command
{
    use TenantAware;

    protected $signature = 'app:refresh-adm-materialized-view {remoteTable} {--indexColumn=sisid} {--tenant=*}';

    protected $description = 'Refresh ADM materialized view';

    public function handle(): void
    {
        try {
            $benchmark = Benchmark::measure(function () {
                $database = DB::connection(config('database.default'));

                $remoteTable = $this->argument('remoteTable');

                $localTable = $remoteTable . '_local';

                $temporaryTable = $localTable . '_tmp';

                $database->unprepared("CREATE MATERIALIZED VIEW {$temporaryTable} AS SELECT * FROM {$remoteTable};");

                $indexColumn = $this->option('indexColumn');

                $database->unprepared("BEGIN TRANSACTION; DROP MATERIALIZED VIEW IF EXISTS {$localTable}; ALTER MATERIALIZED VIEW {$temporaryTable} RENAME TO {$localTable}; CREATE INDEX idx_{$remoteTable}_{$indexColumn} ON {$localTable} ({$indexColumn}); COMMIT;");
            });

            Log::channel('amd_refresh')->info($benchmark, [
                'remoteTable' => $this->argument('remoteTable'),
                'indexColumn' => $this->option('indexColumn'),
                'connection' => config('database.default'),
            ]);
        } catch (Exception $e) {
            // TODO: Notify someone
            Log::channel('amd_refresh')->error($e->getMessage(), [
                'remoteTable' => $this->argument('remoteTable'),
                'indexColumn' => $this->option('indexColumn'),
                'connection' => config('database.default'),
            ]);
        }
    }
}
