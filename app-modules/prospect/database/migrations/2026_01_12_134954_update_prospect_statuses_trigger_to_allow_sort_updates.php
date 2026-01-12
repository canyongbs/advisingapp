<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('prospect_statuses', function (Blueprint $table) {
            $table->trigger(
                name: 'prevent_modification_of_system_protected_rows',
                action: "prevent_modification_of_system_protected_rows('sort', 'updated_at')",
                fire: 'BEFORE UPDATE OR DELETE',
            )
                ->forEachRow()
                ->replace(true);
        });
    }

    public function down(): void
    {
        Schema::table('allow_sort_updates', function (Blueprint $table) {
            $table->trigger(
                name: 'prevent_modification_of_system_protected_rows',
                action: 'prevent_modification_of_system_protected_rows()',
                fire: 'BEFORE UPDATE OR DELETE',
            )
                ->forEachRow()
                ->replace(true);
        });
    }
};
