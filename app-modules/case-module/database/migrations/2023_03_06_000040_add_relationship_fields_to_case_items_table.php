<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipFieldsToCaseItemsTable extends Migration
{
    public function up()
    {
        Schema::table('case_items', function (Blueprint $table) {
            $table->unsignedBigInteger('institution_id')->nullable();
            //$table->foreign('institution_id', 'institution_fk_8136757')->references('id')->on('institutions');
            $table->unsignedBigInteger('state_id')->nullable();
            //$table->foreign('state_id', 'state_fk_8136758')->references('id')->on('case_item_statuses');
            $table->unsignedBigInteger('type_id')->nullable();
            //$table->foreign('type_id', 'type_fk_8136759')->references('id')->on('case_item_types');
            $table->unsignedBigInteger('priority_id')->nullable();
            //$table->foreign('priority_id', 'priority_fk_8136760')->references('id')->on('case_item_priorities');
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            //$table->foreign('assigned_to_id', 'assigned_to_fk_8136761')->references('id')->on('users');
            $table->unsignedBigInteger('created_by_id')->nullable();
            //$table->foreign('created_by_id', 'created_by_fk_8136242')->references('id')->on('users');
        });
    }
}
