<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PrintApiSchema extends Command
{
    protected $signature = 'api:print-schema';

    protected $description = 'Compiles and prints the API schema.';

    public function handle(): void
    {
        $tenant = Tenant::query()->first();

        if (! $tenant) {
            $this->error('No tenant found.');

            return;
        }

        Artisan::call(
            command: "tenants:artisan \"lighthouse:print-schema -W -D public\" --tenant={$tenant->id}",
            outputBuffer: $this->output,
        );
    }
}
