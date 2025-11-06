<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qna_advisor_categories', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    public function down(): void
    {
        Schema::table('qna_advisor_categories', function (Blueprint $table) {
            $table->string('description')->change();
        });
    }
};
