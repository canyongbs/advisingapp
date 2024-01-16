<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateFreshLandlord extends Command
{
    protected $signature = 'migrate:landlord:fresh';

    protected $description = 'Migrate the landlord database fresh.';

    public function handle(): void
    {
        Artisan::call(
            command: 'migrate:fresh --database=landlord --path=database/migrations/landlord',
            outputBuffer: $this->output,
        );
    }
}
