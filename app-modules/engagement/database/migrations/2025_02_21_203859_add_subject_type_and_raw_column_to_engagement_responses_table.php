<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engagement_responses', function (Blueprint $table) {
            $table->string('subject')->nullable();
            $table->string('type')->initial('sms');
            $table->text('raw')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('engagement_responses', function (Blueprint $table) {
            $table->dropColumn(['subject', 'type', 'raw']);
        });
    }
};
