<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKbItemsTable extends Migration
{
    public function up()
    {
        Schema::create('kb_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('question');
            $table->string('public');
            $table->longText('solution')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
