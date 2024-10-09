<?php

namespace AdvisingApp\StudentRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Student;

class ManageableStudent extends Student
{
    protected $table = 'students';
}
