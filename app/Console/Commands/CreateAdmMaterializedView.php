<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAdmMaterializedView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-adm-materialized-view';

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

        $database->statement('DROP MATERIALIZED VIEW IF EXISTS students_local CASCADE;');

        $database->statement('CREATE MATERIALIZED VIEW students_local AS SELECT * FROM students;');

        $database->statement('CREATE UNIQUE INDEX idx_sisid ON students_local (sisid);');

        $database->statement('VACUUM ANALYZE students_local;');

        $database->statement('REFRESH MATERIALIZED VIEW students_local;');
    }
}
