<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJourneyEmailItemsTable extends Migration
{
    public function up()
    {
        Schema::create('journey_email_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('body');
            $table->datetime('start');
            $table->datetime('end')->nullable();
            $table->string('active')->nullable();
            $table->string('frequency');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
