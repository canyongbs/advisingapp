<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
