<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenants:create {name} {domain}';

    protected $description = 'Temporary command to test the tenant creation process.';

    public function handle(): void
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');

        $database = 'tenant_' . strtolower(Str::random(30));

        DB::connection('landlord')->statement("CREATE DATABASE {$database}");

        $sisDatabase = 'tenant_' . strtolower(Str::random(30));

        DB::connection('sis')->statement("CREATE DATABASE {$sisDatabase}");

        Tenant::query()
            ->create(
                [
                    'name' => $name,
                    'domain' => $domain,
                    'db_host' => config('database.connections.landlord.host'),
                    'db_port' => config('database.connections.landlord.port'),
                    'database' => $database,
                    'db_username' => config('database.connections.landlord.username'),
                    'db_password' => config('database.connections.landlord.password'),
                    'sis_db_host' => config('database.connections.sis.host'),
                    'sis_db_port' => config('database.connections.sis.port'),
                    'sis_database' => $sisDatabase,
                    'sis_db_username' => config('database.connections.sis.username'),
                    'sis_db_password' => config('database.connections.sis.password'),
                ]
            );
    }
}
