<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseUpdateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('case_update_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('update');
            $table->string('internal');
            $table->string('direction');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
