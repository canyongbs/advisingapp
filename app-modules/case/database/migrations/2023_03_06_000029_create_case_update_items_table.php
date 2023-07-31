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
            $table->unsignedBigInteger('case_id')->nullable();
            $table->text('update');
            $table->boolean('internal');
            $table->string('direction');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('case_id', 'case_fk_8136752')->references('id')->on('case_items');
        });
    }
}
