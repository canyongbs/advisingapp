<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ai_threads', function (Blueprint $table) {
            $table->dateTime('locked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ai_threads', function (Blueprint $table) {
            $table->dropColumn('locked_at');
        });
    }
};
