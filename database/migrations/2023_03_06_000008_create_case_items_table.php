<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseItemsTable extends Migration
{
    public function up()
    {
        Schema::create('case_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('casenumber');
            $table->longText('close_details')->nullable();
            $table->longText('res_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
