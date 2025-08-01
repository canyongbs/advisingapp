<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEnrollments;

use AdvisingApp\StudentDataModel\Actions\PutStudentEnrollments;
use AdvisingApp\StudentDataModel\DataTransferObjects\StudentEnrollmentData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentEnrollmentResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class PutStudentEnrollmentsController
{
    /**
     * @response StudentEnrollmentResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, PutStudentEnrollments $enrollments, Student $student): JsonResource
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
            'enrollments.*.faculty_email' => ['sometimes', 'email'],
            'enrollments.*.semester_code' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.semester_name' => ['sometimes', 'string', 'max:255'],
            'enrollments.*.start_date' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'enrollments.*.end_date' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $enrollmentsData = StudentEnrollmentData::collect($data['enrollments']);

        $enrollments->execute($student, $enrollmentsData);

        return $student->refresh()->enrollments->toResourceCollection(StudentEnrollmentResource::class);
    }
}
