<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectSourcesTable extends Migration
{
    public function up()
    {
        Schema::create('prospect_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
