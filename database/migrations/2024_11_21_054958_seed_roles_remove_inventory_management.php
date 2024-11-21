<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')
            ->whereIn('name', ['inventory-management.inventory_management'])
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
