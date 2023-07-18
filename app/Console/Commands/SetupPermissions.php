<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\RolesAndPermissions\CreatePermissions;

class SetupPermissions extends Command
{
    protected $signature = 'permissions:setup';

    protected $description = 'This command will create all of the permissions in our custom and model permission definitions.';

    public function handle(): int
    {
        resolve(CreatePermissions::class)->handle();

        return self::SUCCESS;
    }
}
