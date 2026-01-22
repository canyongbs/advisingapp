<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('interaction_statuses', function (Blueprint $table) {
                $table->dropUnique(['name', 'interactable_type']);
                $table->uniqueIndex(['name', 'interactable_type'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('interaction_statuses', function (Blueprint $table) {
                $table->dropUniqueIndex(['name', 'interactable_type']);
                $table->unique(['name', 'interactable_type'])->where('deleted_at IS NULL');
            });
        });
    }
};
