<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectStatusesTable extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('classification');
            $table->string('name');
            $table->string('color');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
