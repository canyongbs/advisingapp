<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipFieldsToKbItemsTable extends Migration
{
    public function up()
    {
        Schema::table('kb_items', function (Blueprint $table) {
            $table->unsignedBigInteger('quality_id')->nullable();
            $table->foreign('quality_id', 'quality_fk_8136762')->references('id')->on('kb_item_qualities');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id', 'status_fk_8136763')->references('id')->on('kb_item_statuses');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id', 'category_fk_8136765')->references('id')->on('kb_item_categories');
        });
    }
}
