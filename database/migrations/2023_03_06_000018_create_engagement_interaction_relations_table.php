<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEngagementInteractionRelationsTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_interaction_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('relation');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
