<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngagementInteractionTypesTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_interaction_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
