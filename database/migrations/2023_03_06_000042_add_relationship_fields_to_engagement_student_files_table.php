<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipFieldsToEngagementStudentFilesTable extends Migration
{
    public function up()
    {
        Schema::table('engagement_student_files', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id', 'student_fk_8136688')->references('id')->on('record_student_items');
        });
    }
}
