<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentProgramResource;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class ListStudentProgramsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<StudentProgramResource>>
     */
    #[Group('Students')]
    #[QueryParameter('filter[acad_career]', description: 'Filter the results where the program\'s Academic Career contains the provided string.', type: 'string')]
    #[QueryParameter('filter[division]', description: 'Filter the results where the program\'s Division contains the provided string.', type: 'string')]
    #[QueryParameter('filter[acad_plan]', description: 'Filter the results where the program\'s Academic Plan contains the provided string.', type: 'string')]
    #[QueryParameter('filter[prog_status]', description: 'Filter the results where the program\'s Program Status contains the provided string.', type: 'string')]
    #[QueryParameter('filter[cum_gpa]', description: 'Filter the results where the program\'s Cumulative GPA matches the provided number using comparison operators. Supported: =, <, <=, >, >=.', type: 'float')]
    #[QueryParameter('filter[semester]', description: 'Filter the results where the program\'s Semester contains the provided string.', type: 'string')]
    #[QueryParameter('filter[descr]', description: 'Filter the results where the program\'s Description contains the provided string.', type: 'string')]
    #[QueryParameter('filter[foi]', description: 'Filter the results where the program\'s Field of Interest contains the provided string.', type: 'string')]
    #[QueryParameter('filter[change_dt]', description: 'Filter the results where the program\'s Change Date matches the provided date using comparison operators. Supported: =, <, <=, >, >=. Format: YYYY-MM-DD H:i:s.', type: 'datetime')]
    #[QueryParameter('filter[declare_dt]', description: 'Filter the results where the program\'s Declare Date matches the provided date using comparison operators. Supported: =, <, <=, >, >=. Format: YYYY-MM-DD H:i:s.', type: 'datetime')]
    #[QueryParameter('page[number]', description: 'Control which page of programs is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many programs are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of programs that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'sisid', examples: [
        'acad_career' => new Example('acad_career'),
        'division' => new Example('division'),
        'acad_plan' => new Example('acad_plan'),
        'prog_status' => new Example('prog_status'),
        'cum_gpa' => new Example('cum_gpa'),
        'semester' => new Example('semester'),
        'descr' => new Example('descr'),
        'foi' => new Example('foi'),
        'change_dt' => new Example('change_dt'),
        'declare_dt' => new Example('declare_dt'),
    ])]
    public function __invoke(Request $request, Student $student): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('view', $student);
        Gate::authorize('viewAny', Program::class);

        return QueryBuilder::for($student->programs())
            ->allowedFilters([
                AllowedFilter::partial('sisid'),
                AllowedFilter::partial('acad_career'),
                AllowedFilter::partial('division'),
                AllowedFilter::callback('acad_plan', function (Builder $query, string $value) {
                    $query->whereRaw('acad_plan::text ILIKE ?', ["%{$value}%"]);
                }),
                AllowedFilter::partial('prog_status'),
                AllowedFilter::operator('cum_gpa', FilterOperator::DYNAMIC),
                AllowedFilter::partial('semester'),
                AllowedFilter::partial('descr'),
                AllowedFilter::partial('foi'),
                AllowedFilter::operator('change_dt', FilterOperator::DYNAMIC),
                AllowedFilter::operator('declare_dt', FilterOperator::DYNAMIC),
                AllowedFilter::operator('graduation_dt', FilterOperator::DYNAMIC),
                AllowedFilter::operator('conferred_dt', FilterOperator::DYNAMIC),
            ])
            ->allowedSorts([
                AllowedSort::field('sisid'),
                AllowedSort::field('acad_career'),
                AllowedSort::field('division'),
                AllowedSort::field('acad_plan'),
                AllowedSort::field('prog_status'),
                AllowedSort::field('cum_gpa'),
                AllowedSort::field('semester'),
                AllowedSort::field('descr'),
                AllowedSort::field('foi'),
                AllowedSort::field('change_dt'),
                AllowedSort::field('declare_dt'),
                AllowedSort::field('graduation_dt'),
                AllowedSort::field('conferred_dt'),
            ])
            ->defaultSort(AllowedSort::field('sisid'))
            ->jsonPaginate()
            ->toResourceCollection(StudentProgramResource::class);
    }
}
