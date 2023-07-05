<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordStudentItemsTable extends Migration
{
    public function up()
    {
        Schema::create('record_student_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sisid');
            $table->string('otherid')->nullable();
            $table->string('first')->nullable();
            $table->string('last')->nullable();
            $table->string('full')->nullable();
            $table->string('preferred')->nullable();
            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->integer('mobile')->nullable();
            $table->string('sms_opt_out')->nullable();
            $table->string('email_bounce')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('hsgrad')->nullable();
            $table->string('dual')->nullable();
            $table->string('ferpa')->nullable();
            $table->float('gpa', 4, 3)->nullable();
            $table->string('dfw')->nullable();
            $table->string('firstgen')->nullable();
            $table->string('ethnicity')->nullable();
            $table->datetime('lastlmslogin')->nullable();
            $table->timestamps();
        });
    }
}
