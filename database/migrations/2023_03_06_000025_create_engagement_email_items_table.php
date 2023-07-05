<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngagementEmailItemsTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_email_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
