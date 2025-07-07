<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    ...existing copyright...

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students;

use AdvisingApp\StudentDataModel\Actions\UpdateStudent;
use AdvisingApp\StudentDataModel\DataTransferObjects\UpdateStudentData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateStudentController
{
    use CanIncludeRelationships;

    /**
     * @response StudentResource
     */
    #[Group('Students')]
    #[QueryParameter('include', description: 'Include related resources in the response.', type: 'string', examples: [
        'email_addresses' => new Example('email_addresses'),
        'primary_email_address' => new Example('primary_email_address'),
    ])]
    public function __invoke(Request $request, UpdateStudent $updateStudent, Student $student): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('update', $student);

        $data = $request->validate([
            'otherid' => ['sometimes', 'max:255'],
            'first' => ['sometimes', 'max:255'],
            'last' => ['sometimes', 'max:255'],
            'full_name' => ['sometimes', 'max:255'],
            'preferred' => ['sometimes', 'max:255'],
            'birthdate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['sometimes', 'numeric'],
            'gender' => ['sometimes', 'max:255'],
            'sms_opt_out' => ['sometimes', 'boolean'],
            'email_bounce' => ['sometimes', 'boolean'],
            'dual' => ['sometimes', 'boolean'],
            'ferpa' => ['sometimes', 'boolean'],
            'firstgen' => ['sometimes', 'boolean'],
            'sap' => ['sometimes', 'boolean'],
            'holds' => ['sometimes', 'max:255'],
            'dfw' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'ethnicity' => ['sometimes', 'max:255'],
            'lastlmslogin' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'f_e_term' => ['sometimes', 'max:255'],
            'mr_e_term' => ['sometimes', 'max:255'],
            'primary_email_id' => ['sometimes', 'uuid:4', Rule::exists(StudentEmailAddress::class, 'id')->where('sisid', $student->sisid)],
        ]);

        $student = $updateStudent->execute($student, UpdateStudentData::from($data));

        return $student
            ->withoutRelations()
            ->load($this->getIncludedRelationshipsToLoad($request, [
                'email_addresses' => 'emailAddresses',
                'primary_email_address' => 'primaryEmailAddress',
            ]))
            ->toResource(StudentResource::class);
    }
}
