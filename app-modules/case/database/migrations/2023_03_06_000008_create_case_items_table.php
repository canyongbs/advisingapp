<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseItemsTable extends Migration
{
    public function up()
    {
        Schema::create('case_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('casenumber');
            $table->string('respondent_type')->nullable();
            $table->string('respondent_id')->nullable();
            $table->longText('close_details')->nullable();
            $table->longText('res_details')->nullable();
            $table->unsignedBigInteger('institution_id')->nullable()->after('res_details');
            $table->unsignedBigInteger('state_id')->nullable()->after('institution_id');
            $table->unsignedBigInteger('type_id')->nullable()->after('state_id');
            $table->unsignedBigInteger('priority_id')->nullable()->after('type_id');
            $table->unsignedBigInteger('assigned_to_id')->nullable()->after('priority_id');
            $table->unsignedBigInteger('created_by_id')->nullable()->after('assigned_to_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
