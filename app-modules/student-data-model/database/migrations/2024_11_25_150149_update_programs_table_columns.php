<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('otherid')->nullable()->change();
            $table->string('acad_career')->nullable()->change();
            $table->string('division')->nullable()->change();
            $table->jsonb('acad_plan')->nullable();
            $table->string('prog_status')->nullable()->change();
            $table->float('cum_gpa', 4, 3)->nullable()->change();
            $table->string('semester')->nullable()->change();
            $table->string('descr')->nullable()->change();
            $table->string('foi')->nullable()->change();
            $table->dateTimeTz('change_dt')->nullable();
            $table->dateTimeTz('declare_dt')->nullable();
            $table->dateTimeTz('graduation_dt')->nullable();
            $table->dateTimeTz('conferred_dt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('otherid')->nullable(false)->change();
            $table->string('acad_career')->nullable(false)->change();
            $table->string('division')->nullable(false)->change();
            $table->string('prog_status')->nullable(false)->change();
            $table->float('cum_gpa', 4, 3)->nullable(false)->change();
            $table->string('semester')->nullable(false)->change();
            $table->string('descr')->nullable(false)->change();
            $table->string('foi')->nullable(false)->change();

            $table->dropColumn(['acad_plan', 'change_dt', 'declare_dt', 'graduation_dt', 'conferred_dt']);
        });
    }
};
