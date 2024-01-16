<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateLandlord extends Command
{
    protected $signature = 'migrate:landlord';

    protected $description = 'Migrate the landlord database.';

    public function handle(): void
    {
        Artisan::call(
            command: 'migrate --database=landlord --path=database/migrations/landlord',
            outputBuffer: $this->output,
        );
    }
}
