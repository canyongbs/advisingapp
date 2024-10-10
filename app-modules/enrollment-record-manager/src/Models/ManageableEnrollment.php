<?php

namespace AdvisingApp\EnrollmentRecordManager\Models;

use AdvisingApp\StudentDataModel\Models\Enrollment;

class ManageableEnrollment extends Enrollment
{
    protected $table = 'enrollments';
}
