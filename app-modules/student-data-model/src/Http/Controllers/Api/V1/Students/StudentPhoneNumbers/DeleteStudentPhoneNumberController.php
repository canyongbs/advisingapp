<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPhoneNumbers;

use AdvisingApp\StudentDataModel\Actions\DeleteStudentPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DeleteStudentPhoneNumberController
{
    #[Group('Students')]
    public function __invoke(DeleteStudentPhoneNumber $deleteStudentPhoneNumber, Student $student, StudentPhoneNumber $studentPhoneNumber): Response
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('update', $student);

        $deleteStudentPhoneNumber->execute($studentPhoneNumber);

        return response()->noContent(204);
    }
}
