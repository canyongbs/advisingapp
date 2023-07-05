<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbItemQualitiesTable extends Migration
{
    public function up()
    {
        Schema::create('kb_item_qualities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rating');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
