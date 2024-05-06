<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('tenants', 'setup_complete')) {
            DB::table('tenants')->update(['setup_complete' => true]);
        }
    }

    public function down(): void
    {
        // nop
    }
};
