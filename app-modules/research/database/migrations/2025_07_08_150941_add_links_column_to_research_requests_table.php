<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('research_requests', function (Blueprint $table) {
            $table->jsonb('links')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('research_requests', function (Blueprint $table) {
            $table->dropColumn('links');
        });
    }
};
