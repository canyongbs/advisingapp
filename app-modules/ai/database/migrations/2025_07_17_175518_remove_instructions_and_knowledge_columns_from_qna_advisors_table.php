<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('qna_advisors', function (Blueprint $table) {
            $table->dropColumn(['instructions', 'knowledge']);
        });
    }

    public function down(): void
    {
        Schema::table('qna_advisors', function (Blueprint $table) {
            $table->longText('instructions')->nullable();
            $table->longText('knowledge')->nullable();
        });
    }
};
