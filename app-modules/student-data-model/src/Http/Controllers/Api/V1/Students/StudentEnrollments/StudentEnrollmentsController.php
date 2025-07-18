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
            'enrollment' => ['required', 'array'],
            'enrollment.*.division' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.class_nbr' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.crse_grade_off' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.unt_taken' => ['sometimes', 'numeric'],
            'enrollment.*.unt_earned' => ['sometimes', 'numeric'],
            'enrollment.*.last_upd_dt_stmp' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'enrollment.*.section' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.name' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.department' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.faculty_name' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.faculty_email' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.semester_code' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.semester_name' => ['sometimes', 'string', 'max:255'],
            'enrollment.*.start_date' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'enrollment.*.end_date' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $studentEnrollmentRequestData = StudentEnrollmentRequestData::from([
            'enrollment' => $data['enrollment'],
        ]);

        $enrollments = $enrollment->execute($student, $studentEnrollmentRequestData);

        return $enrollments->toResourceCollection(StudentEnrollmentResource::class);
    }
}
