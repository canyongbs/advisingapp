<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseUpdateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('case_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('case_id')->nullable()->references('id')->on('case_items');
            $table->text('update');
            $table->boolean('internal');
            $table->string('direction');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
