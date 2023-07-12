<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEngagementInteractionItemsTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_interaction_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('direction');
            $table->datetime('start');
            $table->string('duration');
            $table->string('subject');
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
