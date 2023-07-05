<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToProspectItemsTable extends Migration
{
    public function up()
    {
        Schema::table('prospect_items', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id', 'status_fk_8137165')->references('id')->on('prospect_statuses');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreign('source_id', 'source_fk_8137166')->references('id')->on('prospect_sources');
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->foreign('assigned_to_id', 'assigned_to_fk_8136738')->references('id')->on('users');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_8136739')->references('id')->on('users');
        });
    }
}
