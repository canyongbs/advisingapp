<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEnrollments;

use AdvisingApp\StudentDataModel\Actions\UpdateStudentEnrollment;
use AdvisingApp\StudentDataModel\DataTransferObjects\StudentEnrollmentRequestData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentEnrollmentResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class StudentEnrollmentsController
{
    /**
     * @response StudentEnrollmentResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, UpdateStudentEnrollment $enrollment, Student $student): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('viewAny', Enrollment::class);
        Gate::authorize('create', Enrollment::class);

        foreach ($student->enrollments as $enrollmentToUpdate) {
            Gate::authorize('update', $enrollmentToUpdate);
        }

        foreach ($student->enrollments as $enrollmentToDelete) {
            Gate::authorize('delete', $enrollmentToDelete);
        }

        $data = $request->validate([
            'enrollments' => ['required', 'array'],
            'enrollments.*.division' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.class_nbr' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.crse_grade_off' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.unt_taken' => ['sometimes', 'numeric'],
            'enrollments.*.unt_earned' => ['sometimes', 'numeric'],
            'enrollments.*.last_upd_dt_stmp' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'enrollments.*.section' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.name' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.department' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.faculty_name' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.faculty_email' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.semester_code' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.semester_name' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.start_date' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'enrollments.*.end_date' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $studentEnrollmentRequestData = StudentEnrollmentRequestData::from([
            'enrollments' => $data['enrollments'],
        ]);

        $enrollments = $enrollment->execute($student, $studentEnrollmentRequestData);

        return $enrollments->toResourceCollection(StudentEnrollmentResource::class);
    }
}
