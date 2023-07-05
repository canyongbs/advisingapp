<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngagementStudentFilesTable extends Migration
{
    public function up()
    {
        Schema::create('engagement_student_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
