<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJourneyItemsTable extends Migration
{
    public function up()
    {
        Schema::create('journey_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->longText('body');
            $table->datetime('start');
            $table->datetime('end');
            $table->string('frequency');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
