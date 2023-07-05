<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngagementInteractionDriversTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_interaction_drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('driver');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
