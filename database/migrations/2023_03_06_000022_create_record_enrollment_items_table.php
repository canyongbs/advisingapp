<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordEnrollmentItemsTable extends Migration
{
    public function up()
    {
        Schema::create('record_enrollment_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sisid');
            $table->string('name')->nullable();
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->string('course')->nullable();
            $table->float('grade', 2, 1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
