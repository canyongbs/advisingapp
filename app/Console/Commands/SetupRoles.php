<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\RolesAndPermissions\CreateRoles;

class SetupRoles extends Command
{
    protected $signature = 'roles:setup';

    protected $description = 'This command will create all of the roles defined in the roles config directory.';

    public function handle(): int
    {
        resolve(CreateRoles::class)->handle();

        return self::SUCCESS;
    }
}
