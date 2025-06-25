<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_message_files', function (Blueprint $table) {
            $table->foreignUuid('message_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ai_message_files', function (Blueprint $table) {
            $table->foreignUuid('message_id')->nullable(false)->change();
        });
    }
};
