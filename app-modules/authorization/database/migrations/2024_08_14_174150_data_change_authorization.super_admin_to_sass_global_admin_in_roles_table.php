<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('roles')->where('name', 'authorization.super_admin')->update(['name' => 'SaaS Global Admin']);
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'SaaS Global Admin')->update(['name' => 'authorization.super_admin']);
    }
};
