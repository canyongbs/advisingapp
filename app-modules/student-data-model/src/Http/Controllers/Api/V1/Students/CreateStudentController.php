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

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students;

use AdvisingApp\StudentDataModel\Actions\CreateStudent;
use AdvisingApp\StudentDataModel\DataTransferObjects\CreateStudentData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CreateStudentController
{
    use CanIncludeRelationships;

    /**
     * @response StudentResource
     */
    #[QueryParameter('include', description: 'Include related resources in the response.', type: 'string', examples: [
        'email_addresses' => new Example('email_addresses'),
        'primary_email_address' => new Example('primary_email_address'),
    ])]
    public function __invoke(Request $request, CreateStudent $createStudent): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('create', Student::class);

        $data = $request->validate([
            'sisid' => ['required', 'max:255', 'alpha_dash', Rule::unique('students', 'sisid')],
            'otherid' => ['sometimes', 'max:255'],
            'first' => ['required', 'max:255'],
            'last' => ['required', 'max:255'],
            'full_name' => ['required', 'max:255'],
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
            'email_addresses' => ['sometimes', 'array'],
            'email_addresses.*' => ['array'],
            'email_addresses.*.address' => ['required', 'email'],
            'email_addresses.*.type' => ['sometimes', 'max:255'],
        ]);

        $student = $createStudent->execute(CreateStudentData::from($data));

        return $student
            ->withoutRelations()
            ->load($this->getIncludedRelationshipsToLoad($request, [
                'email_addresses' => 'emailAddresses',
                'primary_email_address' => 'primaryEmailAddress',
            ]))
            ->toResource(StudentResource::class);
    }
}
