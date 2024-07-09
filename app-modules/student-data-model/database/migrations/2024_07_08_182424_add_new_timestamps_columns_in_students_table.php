<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->timestamps();
            $table->timestamp('created_at_source')->nullable();
            $table->timestamp('updated_at_source')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropColumn('created_at_source');
            $table->dropColumn('updated_at_source');
        });
    }
};
