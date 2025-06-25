<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ai_assistant_files', function (Blueprint $table) {
            $table->longText('parsing_results')->nullable();
        });

        Schema::table('ai_message_files', function (Blueprint $table) {
            $table->longText('parsing_results')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ai_assistant_files', function (Blueprint $table) {
            $table->dropColumn('parsing_results');
        });

        Schema::table('ai_message_files', function (Blueprint $table) {
            $table->dropColumn('parsing_results');
        });
    }
};
