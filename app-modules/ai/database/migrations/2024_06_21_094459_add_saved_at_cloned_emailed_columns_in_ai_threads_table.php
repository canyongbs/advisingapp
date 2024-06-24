<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('ai_threads', function (Blueprint $table) {
            $table->dateTime('saved_at')->nullable();
            $table->integer('cloned')->default(0);
            $table->integer('emailed')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('ai_threads', function (Blueprint $table) {
            $table->dropColumn('saved_at');
            $table->dropColumn('cloned');
            $table->dropColumn('emailed');
        });
    }
};
