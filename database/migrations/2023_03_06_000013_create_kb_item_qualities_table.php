<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
