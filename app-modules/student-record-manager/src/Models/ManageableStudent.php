<?php

namespace AdvisingApp\StudentRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageableStudent extends Student
{
    protected $table = 'students';
}
