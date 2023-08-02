<?php

namespace Assist\Authorization\Tests;

use Illuminate\Support\Facades\DB;

class Helpers
{
    public function truncateTables(): void
    {
        DB::table('roles')->truncate();
        DB::table('role_groups')->truncate();
        DB::table('role_group_user')->truncate();
        DB::table('role_role_group')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
    }
}
