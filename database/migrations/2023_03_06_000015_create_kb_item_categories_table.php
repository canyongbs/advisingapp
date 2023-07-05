<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbItemCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('kb_item_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
