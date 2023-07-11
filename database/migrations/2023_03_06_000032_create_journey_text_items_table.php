<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJourneyTextItemsTable extends Migration
{
    public function up()
    {
        Schema::create('journey_text_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('text')->nullable();
            $table->datetime('start')->nullable();
            $table->datetime('end')->nullable();
            $table->string('active');
            $table->string('frequency')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
