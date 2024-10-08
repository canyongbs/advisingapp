<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('prospect_statuses', function (Blueprint $table) {
            $table->boolean('is_system_protected')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('prospect_statuses', function (Blueprint $table) {
            $table->dropColumn('is_system_protected');
        });
    }
};
