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

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms;

use AdvisingApp\StudentDataModel\Actions\PutStudentPrograms;
use AdvisingApp\StudentDataModel\DataTransferObjects\StudentProgramData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentProgramResource;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class PutStudentProgramsController
{
    /**
     * @response StudentProgramResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, PutStudentPrograms $programs, Student $student): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('viewAny', Program::class);
        Gate::authorize('create', Program::class);

        foreach ($student->programs as $programToUpdate) {
            Gate::authorize('update', $programToUpdate);
        }

        foreach ($student->programs as $programToDelete) {
            Gate::authorize('delete', $programToDelete);
        }

        $data = $request->validate([
            'programs' => ['required', 'array'],
            'programs.*.acad_career' => ['sometimes', 'string', 'max:255'],
            'programs.*.division' => ['sometimes', 'string', 'max:255'],
            'programs.*.acad_plan' => ['required', 'array'],
            'programs.*.prog_status' => ['sometimes', 'string', 'max:255'],
            'programs.*.cum_gpa' => ['sometimes', 'decimal:0,2'],
            'programs.*.semester' => ['sometimes', 'string', 'max:255'],
            'programs.*.descr' => ['sometimes', 'string', 'max:255'],
            'programs.*.foi' => ['sometimes', 'string', 'max:255'],
            'programs.*.change_dt' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'programs.*.declare_dt' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $programsData = StudentProgramData::collect($data['programs']);

        $programs->execute($student, $programsData);

        return $student->refresh()->programs->toResourceCollection(StudentProgramResource::class);
    }
}
