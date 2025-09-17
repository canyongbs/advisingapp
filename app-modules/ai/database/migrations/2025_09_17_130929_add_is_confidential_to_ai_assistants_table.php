<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->boolean('is_confidential')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('ai_assistants', function (Blueprint $table) {
            $table->dropColumn('is_confidential');
        });
    }
};
