<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupForeignDataWrapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-foreign-data-wrapper';

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
        DB::connection('pgsql')->statement('CREATE EXTENSION IF NOT EXISTS postgres_fdw;');

        DB::connection('pgsql')->statement("
            CREATE SERVER IF NOT EXISTS sis_bridge 
            FOREIGN DATA WRAPPER postgres_fdw
            OPTIONS (host 'redshift', dbname 'sis', port '5433');
        ");

        DB::connection('pgsql')->statement("
            CREATE USER MAPPING IF NOT EXISTS FOR CURRENT_USER 
            SERVER sis_bridge
            OPTIONS (user 'sail', password 'password');
        ");

        $tableExists = DB::connection('pgsql')->select("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE  table_schema = 'public'
            AND    table_name   = 'students'
        );")[0]->exists;

        if (! $tableExists) {
            DB::connection('pgsql')->statement('
            IMPORT FOREIGN SCHEMA public
            LIMIT TO (students)
            FROM SERVER sis_bridge
            INTO public;
        ');
        }
    }
}
