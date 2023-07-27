<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipFieldsToCaseUpdateItemsTable extends Migration
{
    public function up()
    {
        Schema::table('case_update_items', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id', 'student_fk_8136747')->references('id')->on('record_student_items');
            $table->unsignedBigInteger('case_id')->nullable();
            $table->foreign('case_id', 'case_fk_8136752')->references('id')->on('case_items');
        });
    }
}
