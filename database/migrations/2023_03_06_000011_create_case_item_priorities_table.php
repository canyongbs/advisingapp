<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseItemPrioritiesTable extends Migration
{
    public function up()
    {
        Schema::create('case_item_priorities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('priority');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
