<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAlertsTable extends Migration
{
    public function up()
    {
        Schema::create('user_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('message')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
