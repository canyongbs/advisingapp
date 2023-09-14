<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::connection('sis')
            ->create('enrollments', function (Blueprint $table) {
                $table->string('sisid');
                $table->string('acad_career');
                $table->string('division');
                $table->string('semester');
                $table->string('class_nbr');
                $table->string('subject');
                $table->string('catalog_nbr');
                $table->string('enrl_status_reason');
                $table->dateTimeTz('enrl_add_dt');
                $table->dateTimeTz('enrl_drop_dt');
                $table->string('crse_grade_off');
                $table->integer('unt_taken');
                $table->unsignedInteger('unt_earned');
                $table->unsignedInteger('unt_earned');
                $table->dateTimeTz('last_upd_dt_stmp');
            });
    }
};
