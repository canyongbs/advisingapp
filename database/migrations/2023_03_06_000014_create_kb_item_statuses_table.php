<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKbItemStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('kb_item_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
