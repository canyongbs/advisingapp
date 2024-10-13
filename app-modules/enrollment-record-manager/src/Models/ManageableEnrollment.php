<?php

namespace AdvisingApp\EnrollmentRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageableEnrollment extends Enrollment
{
    use SoftDeletes;

    protected $table = 'enrollments';
}
