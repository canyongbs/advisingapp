<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

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

class RefreshAdmMaterializedView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-adm-materialized-view {remoteTable} {--indexColumn=sisid} {--connection=pgsql}';

    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $benchmark = Benchmark::measure(function () {
                $database = DB::connection($this->option('connection'));

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
                'connection' => $this->option('connection'),
            ]);
        } catch (Exception $e) {
            // TODO: Notify someone
            Log::channel('amd_refresh')->error($e->getMessage(), [
                'remoteTable' => $this->argument('remoteTable'),
                'indexColumn' => $this->option('indexColumn'),
                'connection' => $this->option('connection'),
            ]);
        }
    }
}
