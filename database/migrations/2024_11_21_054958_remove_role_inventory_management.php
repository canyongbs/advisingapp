<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('roles')
            ->whereIn('name', ['inventory-management.inventory_management'])
            ->delete();
    }

    public function down(): void {}
};
