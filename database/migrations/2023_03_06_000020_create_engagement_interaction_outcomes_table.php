<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngagementInteractionOutcomesTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_interaction_outcomes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('outcome');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
