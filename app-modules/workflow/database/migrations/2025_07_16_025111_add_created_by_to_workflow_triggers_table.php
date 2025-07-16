<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('workflow_triggers', function (Blueprint $table) {
            $table->uuidMorphs('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_triggers', function (Blueprint $table) {
            $table->dropIndex(['created_by_type', 'created_by_id']);
            $table->dropColumn(['created_by_type', 'created_by_id']);
        });
    }
};
