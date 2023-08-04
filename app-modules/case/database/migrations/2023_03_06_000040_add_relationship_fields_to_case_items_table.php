<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipFieldsToCaseItemsTable extends Migration
{
    public function up(): void
    {
        Schema::table('case_items', function (Blueprint $table) {
            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('state_id')->references('id')->on('case_item_statuses');
            $table->foreign('type_id')->references('id')->on('case_item_types');
            $table->foreign('priority_id')->references('id')->on('case_item_priorities');
            $table->foreign('assigned_to_id')->references('id')->on('users');
            $table->foreign('created_by_id')->references('id')->on('users');
        });
    }
}
