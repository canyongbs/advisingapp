<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engagement_responses', function (Blueprint $table) {
            $table->index(['sender_type', 'sender_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('engagement_responses', function (Blueprint $table) {
            $table->dropIndex(['sender_type', 'sender_id', 'status']);
        });
    }
};
