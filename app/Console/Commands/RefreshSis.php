<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class RefreshSis extends Command
{
    use TenantAware;

    protected $signature = 'sis:refresh {--tenant=*}';

    protected $description = 'Refreshes the local SIS database.';

    public function handle(): void
    {
        if (! app()->environment('local')) {
            $this->error('This command can only be run in the local environment.');

            return;
        }

        $this->line('Refreshing SIS database...');

        $tenant = Tenant::current()->id;

        Artisan::call("sis:clear --tenant={$tenant}");

        $this->info('SIS database refreshed');

        $this->line('Loading pre-seeded SIS data...');

        $password = config('database.connections.sis.password');
        $host = config('database.connections.sis.host');
        $port = config('database.connections.sis.port');
        $database = config('database.connections.sis.database');
        $username = config('database.connections.sis.username');

        $result = Process::run("gunzip < ./resources/sql/advising-app-adm-data.gz | PGPASSWORD={$password} psql -h {$host} -p {$port} -U {$username} -d {$database} -q")
            ->throw();

        if ($result->failed()) {
            $this->error($result->output());

            return;
        }

        if (! empty($result->output())) {
            $this->info($result->output());
        }

        $this->info('Pre-seeded SIS data loaded');
    }
}
