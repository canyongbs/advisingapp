<?php

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
