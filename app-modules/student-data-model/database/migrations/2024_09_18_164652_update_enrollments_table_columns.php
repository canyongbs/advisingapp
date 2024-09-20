<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'acad_career',
                'semester',
                'subject',
                'catalog_nbr',
                'enrl_status_reason',
                'enrl_add_dt',
                'enrl_drop_dt',
            ]);

            $table->string('division')->nullable()->change();
            $table->string('class_nbr')->nullable()->change();
            $table->string('crse_grade_off')->nullable()->change();
            $table->integer('unt_taken')->nullable()->change();
            $table->integer('unt_earned')->nullable()->change();
            $table->dateTimeTz('last_upd_dt_stmp')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('division')->nullable(false)->change();
            $table->string('class_nbr')->nullable(false)->change();
            $table->string('crse_grade_off')->nullable(false)->change();
            $table->integer('unt_taken')->nullable(false)->change();
            $table->integer('unt_earned')->nullable(false)->change();
            $table->dateTimeTz('last_upd_dt_stmp')->nullable(false)->change();

            $table->string('acad_career')->nullable();
            $table->string('semester')->nullable();
            $table->string('subject')->nullable();
            $table->string('catalog_nbr')->nullable();
            $table->string('enrl_status_reason')->nullable();
            $table->dateTimeTz('enrl_add_dt')->nullable();
            $table->dateTimeTz('enrl_drop_dt')->nullable();
        });
    }
};
