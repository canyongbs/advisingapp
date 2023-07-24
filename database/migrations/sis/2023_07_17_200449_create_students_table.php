<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('sis')
            ->create('students', function (Blueprint $table) {
                $table->integer('student_id');
                $table->string('first_name');
                $table->string('middle_name');
                $table->string('last_name');
                $table->string('email');
                $table->timestamps();
            });
    }
};
