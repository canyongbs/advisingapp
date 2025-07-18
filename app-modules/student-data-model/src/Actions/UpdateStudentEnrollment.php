<?php

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\DataTransferObjects\StudentEnrollmentRequestData;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UpdateStudentEnrollment
{
    /**
     * @return Collection<int, Enrollment>
     */
    public function execute(Student $student, StudentEnrollmentRequestData $requestData): Collection
    {
        $student->enrollments()->delete();

        $enrollments = collect($requestData->enrollment->toArray());

        return DB::transaction(function () use ($enrollments, $student) {
            return $enrollments->map(function (array $data) use ($student) {
                $enrollment = new Enrollment();
                $enrollment->student()->associate($student);
                $enrollment->fill($data);
                $enrollment->save();

                return $enrollment;
            })->values();
        });
    }
}