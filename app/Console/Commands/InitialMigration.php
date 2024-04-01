<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Command\Command as CommandAlias;

class InitialMigration extends Command
{
    protected $signature = 'app:initial-migration';

    protected $description = 'Checks if the initial Landlord migration has been run and runs it if not.';

    public function handle(): int
    {
        Schema::connection('landlord')->hasTable('migrations')
            ? $this->info('The initial Landlord migration has already been run.')
            : $this->call('migrate', ['--database' => 'landlord', '--path' => 'database/landlord']);

        return CommandAlias::SUCCESS;
    }
}
