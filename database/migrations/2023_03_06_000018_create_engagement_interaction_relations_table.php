<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
