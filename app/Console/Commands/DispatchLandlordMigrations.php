<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use App\Jobs\LandlordSchemaMigration;
use Illuminate\Support\Facades\Process;
use App\Jobs\DispatchLandlordDataMigrations;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DispatchLandlordMigrations extends Command
{
    protected $signature = 'app:dispatch-landlord-migrations';

    protected $description = 'Dispatches Landlord schema and data migrations.';

    public function handle(): int
    {
        $process = Process::run('git describe --tags --abbrev=0');

        if ($process->successful()) {
            $tag = rtrim($process->output());
        } else {
            $this->error($process->errorOutput());

            return CommandAlias::FAILURE;
        }

        $process = Process::run('git log --pretty="%h" -n1 HEAD');

        if ($process->successful()) {
            $shortHash = rtrim($process->output());
        } else {
            $this->error($process->errorOutput());

            return CommandAlias::FAILURE;
        }

        // TODO: Add a catch to notify someone if the batch failed.
        Bus::batch(
            [
                [
                    new LandlordSchemaMigration(),
                    new DispatchLandlordDataMigrations(),
                ],
            ]
        )
            ->name("{$tag}-{$shortHash}")
            ->dispatch();

        return CommandAlias::SUCCESS;
    }
}
