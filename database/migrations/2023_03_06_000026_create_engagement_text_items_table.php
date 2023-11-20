<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEngagementTextItemsTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_text_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('direction')->nullable();
            $table->integer('mobile');
            $table->string('message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
