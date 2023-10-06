<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshAdmMaterializedView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-adm-materialized-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = DB::connection('pgsql');

        $database->statement('REFRESH MATERIALIZED VIEW CONCURRENTLY students_local;');

        $database->statement('REFRESH MATERIALIZED VIEW programs_local;');

        $database->statement('REFRESH MATERIALIZED VIEW enrollments_local;');

        $database->statement('REFRESH MATERIALIZED VIEW performance_local;');
    }
}
