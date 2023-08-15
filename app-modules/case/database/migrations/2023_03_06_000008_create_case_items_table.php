<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('case_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('casenumber');
            $table->string('respondent_type')->nullable();
            $table->string('respondent_id')->nullable();
            $table->longText('close_details')->nullable();
            $table->longText('res_details')->nullable();
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->uuid('status_id')->nullable();
            $table->uuid('type_id')->nullable();
            $table->uuid('priority_id')->nullable();
            $table->uuid('assigned_to_id')->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
