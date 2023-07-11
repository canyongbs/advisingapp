<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
