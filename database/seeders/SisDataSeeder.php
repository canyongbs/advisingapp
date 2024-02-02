<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;

class SisDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->comment('Seeding SIS data...');

        $password = config('database.connections.tenant.password');
        $host = config('database.connections.tenant.host');
        $port = config('database.connections.tenant.port');
        $database = config('database.connections.tenant.database');
        $username = config('database.connections.tenant.username');

        Process::run("gunzip < ./resources/sql/advising-app-adm-data.gz | PGPASSWORD={$password} psql -h {$host} -p {$port} -U {$username} -d {$database} -q")
            ->throw();

        $this->command->comment('Seeding SIS data complete!');
    }
}
