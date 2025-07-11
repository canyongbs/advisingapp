<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPhoneNumbers;

use AdvisingApp\StudentDataModel\Actions\UpdateStudentPhoneNumber;
use AdvisingApp\StudentDataModel\DataTransferObjects\UpdateStudentPhoneNumberData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentPhoneNumberResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class UpdateStudentPhoneNumberController
{
    use CanIncludeRelationships;

    /**
     * @response StudentPhoneNumberResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, UpdateStudentPhoneNumber $updateStudentPhoneNumber, Student $student, StudentPhoneNumber $studentPhoneNumber): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('update', $student);

        $data = $request->validate([
            'number' => ['required'],
            'type' => ['sometimes', 'max:255'],
            'order' => ['sometimes', 'integer'],
            'ext' => ['sometimes', 'integer'],
            'can_receive_sms' => ['sometimes', 'boolean'],
        ]);

        $student = $updateStudentPhoneNumber->execute($studentPhoneNumber, UpdateStudentPhoneNumberData::from($data));

        return $student->toResource(StudentPhoneNumberResource::class);
    }
}
