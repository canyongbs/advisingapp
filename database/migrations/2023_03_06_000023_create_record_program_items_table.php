<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordProgramItemsTable extends Migration
{
    public function up()
    {
        Schema::create('record_program_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('institution')->nullable();
            $table->string('plan')->nullable();
            $table->string('career')->nullable();
            $table->string('term')->nullable();
            $table->string('status')->nullable();
            $table->string('foi')->nullable();
            $table->float('gpa', 4, 3)->nullable();
            $table->timestamps();
        });
    }
}
