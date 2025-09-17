<?php

namespace AdvisingApp\StudentDataModel\Observers;

use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use Illuminate\Support\Facades\DB;

class EnrollmentSemesterObserver
{
    public function creating(EnrollmentSemester $enrollmentSemester): void
    {
        if (blank($enrollmentSemester->order)) {
            $enrollmentSemester->order = DB::raw("(SELECT COALESCE(MAX(\"{$enrollmentSemester->getTable()}\".order), 0) + 1 FROM \"{$enrollmentSemester->getTable()}\")");
        }
    }
}
