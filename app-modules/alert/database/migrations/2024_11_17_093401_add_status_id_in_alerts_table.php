<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->foreignUuid('status_id')->default('9d7ab759-f83a-4357-91a9-d1a6b489271c')->constrained('alert_statuses');
            $table->string('status')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->string('status')->nullable(false)->change();
        });
    }
};
