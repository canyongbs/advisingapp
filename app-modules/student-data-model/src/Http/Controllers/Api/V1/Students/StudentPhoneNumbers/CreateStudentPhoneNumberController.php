<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPhoneNumbers;

use AdvisingApp\StudentDataModel\Actions\CreateStudentPhoneNumber;
use AdvisingApp\StudentDataModel\DataTransferObjects\CreateStudentPhoneNumberData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentPhoneNumberResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CreateStudentPhoneNumberController
{
    /**
     * @response StudentPhoneNumberResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, CreateStudentPhoneNumber $createStudentPhoneNumber, Student $student): JsonResource
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
        $studentPhoneNumber = $createStudentPhoneNumber->execute($student, CreateStudentPhoneNumberData::from($data));

        return $studentPhoneNumber->toResource(StudentPhoneNumberResource::class);
    }
}
