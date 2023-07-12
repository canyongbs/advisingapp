<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
