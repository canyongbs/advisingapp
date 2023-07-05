<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectItemsTable extends Migration
{
    public function up()
    {
        Schema::create('prospect_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first');
            $table->string('last');
            $table->string('full');
            $table->string('preferred')->nullable();
            $table->longText('description')->nullable();
            $table->string('email')->nullable();
            $table->string('email_2')->nullable();
            $table->integer('mobile')->nullable();
            $table->string('sms_opt_out')->nullable();
            $table->string('email_bounce')->nullable();
            $table->integer('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('hsgrad')->nullable();
            $table->date('hsdate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
