<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class ClearSisDatabase extends Command
{
    use TenantAware;

    protected $signature = 'sis:clear {--tenant=*}';

    protected $description = 'Clears the local SIS database.';

    public function handle()
    {
        if (! app()->environment('local')) {
            $this->error('This command can only be run in the local environment.');

            return;
        }

        $this->line('Clearing SIS database...');

        Artisan::call('migrate:fresh --database=sis --path=app-modules/student-data-model/database/migrations/sis');

        $this->info('SIS database cleared');
    }
}
