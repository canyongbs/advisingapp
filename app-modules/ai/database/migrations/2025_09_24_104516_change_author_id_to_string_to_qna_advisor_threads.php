<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('qna_advisor_threads', function (Blueprint $table) {
            $table->string('author_id')->nullable(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('qna_advisor_threads', function (Blueprint $table) {
            $table->uuid('author_id')->nullable(true)->change();
        });
    }
};
