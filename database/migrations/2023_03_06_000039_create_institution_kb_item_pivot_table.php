<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstitutionKbItemPivotTable extends Migration
{
    public function up()
    {
        Schema::create('institution_kb_item', function (Blueprint $table) {
            $table->unsignedBigInteger('kb_item_id');
            $table->foreign('kb_item_id', 'kb_item_id_fk_8136766')->references('id')->on('kb_items')->onDelete('cascade');
            $table->unsignedBigInteger('institution_id');
            $table->foreign('institution_id', 'institution_id_fk_8136766')->references('id')->on('institutions')->onDelete('cascade');
        });
    }
}
