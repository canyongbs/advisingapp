<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseItemStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('case_item_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
