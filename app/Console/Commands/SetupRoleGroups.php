<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\RolesAndPermissions\SyncRoleGroups;

class SetupRoleGroups extends Command
{
    protected $signature = 'role-groups:setup';

    protected $description = 'This command will create all of the role groups defined in the role_groups config directory.';

    public function handle(): int
    {
        resolve(SyncRoleGroups::class)->handle();

        return self::SUCCESS;
    }
}
