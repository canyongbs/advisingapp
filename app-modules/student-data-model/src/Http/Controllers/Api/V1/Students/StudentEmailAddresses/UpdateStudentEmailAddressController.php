<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    ...existing copyright...

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEmailAddresses;

use AdvisingApp\StudentDataModel\Actions\UpdateStudentEmailAddress;
use AdvisingApp\StudentDataModel\DataTransferObjects\UpdateStudentEmailAddressData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentEmailAddressResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class UpdateStudentEmailAddressController
{
    use CanIncludeRelationships;

    /**
     * @response StudentEmailAddressResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, UpdateStudentEmailAddress $updateStudentEmailAddress, Student $student, StudentEmailAddress $studentEmailAddress): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('update', $student);

        $data = $request->validate([
            'address' => ['sometimes', 'email'],
            'type' => ['sometimes', 'max:255'],
            'order' => ['sometimes', 'integer'],
        ]);

        $student = $updateStudentEmailAddress->execute($studentEmailAddress, UpdateStudentEmailAddressData::from($data));

        return $student->toResource(StudentEmailAddressResource::class);
    }
}
