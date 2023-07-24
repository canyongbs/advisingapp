<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseItemStatusesTable extends Migration
{
    public function up(): void
    {
        Schema::create('case_item_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('color');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
