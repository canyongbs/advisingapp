<?php

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\DataTransferObjects\StudentEnrollmentData;
use AdvisingApp\StudentDataModel\DataTransferObjects\StudentEnrollmentRequestData;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PutStudentEnrollments
{
    /**
     * @param Student $student
     * @param array<StudentEnrollmentData> $requestData
     */
    public function execute(Student $student, array $requestData): void
    {
        DB::transaction(function () use ($requestData, $student) {
            $student->enrollments()->delete();

            foreach ($requestData as $enrollmentData) {
                $enrollment = new Enrollment();
                $enrollment->fill($enrollmentData->toArray());
                $enrollment->student()->associate($student);
                $enrollment->save();
            }
        });
    }
}