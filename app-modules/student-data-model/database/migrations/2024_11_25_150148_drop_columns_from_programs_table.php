<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['acad_plan', 'change_dt', 'declare_dt']);
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('acad_plan')->nullable();
            $table->dateTimeTz('change_dt')->nullable();
            $table->dateTimeTz('declare_dt')->nullable();
        });
    }
};
