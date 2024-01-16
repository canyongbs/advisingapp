<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateHelperFiles extends Command
{
    protected $signature = 'app:generate-helper-files';

    protected $description = 'Generate helper files.';

    public function handle(): void
    {
        $tenant = Tenant::query()->first();

        if (! $tenant) {
            $this->error('No tenant found.');

            return;
        }

        Artisan::call(
            command: "tenants:artisan \"ide-helper:generate\" --tenant={$tenant->id}",
            outputBuffer: $this->output,
        );

        Artisan::call(
            command: "tenants:artisan \"ide-helper:models -M\" --tenant={$tenant->id}",
            outputBuffer: $this->output,
        );

        Artisan::call(
            command: "tenants:artisan \"ide-helper:meta\" --tenant={$tenant->id}",
            outputBuffer: $this->output,
        );

        $this->info('Helper files generated!');
    }
}
