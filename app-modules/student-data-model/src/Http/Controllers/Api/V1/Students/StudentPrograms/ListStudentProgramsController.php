<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms;

use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentProgramResource;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
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
     * @response AnonymousResourceCollection<LengthAwarePaginator<StudentProgramsResource>>
     */
    #[Group('Students')]
    #[QueryParameter('filter[sisid]', description: 'Filter the results where the program\'s SISID contains the provided string.', type: 'string')]
    #[QueryParameter('filter[acad_career]', description: 'Filter the results where the program\'s academic career contains the provided string.', type: 'string')]
    #[QueryParameter('filter[division]', description: 'Filter the results where the program\'s division contains the provided string.', type: 'string')]
    #[QueryParameter('filter[acad_plan]', description: 'Filter the results where the program\'s acad_plan contains the provided string.', type: 'string')]
    #[QueryParameter('filter[prog_status]', description: 'Filter the results where the program\'s prog_status contains the provided string.', type: 'string')]
    #[QueryParameter('filter[cum_gpa]', description: 'Filter the results where the program\'s cum_gpa contains the provided number.', type: 'float')]
    #[QueryParameter('filter[semester]', description: 'Filter the results where the program\'s semester contains the provided string.', type: 'string')]
    #[QueryParameter('filter[descr]', description: 'Filter the results where the program\'s descr contains the provided string.', type: 'string')]
    #[QueryParameter('filter[foi]', description: 'Filter the results where the program\'s foi contains the provided string.', type: 'string')]
    #[QueryParameter('filter[change_dt]', description: 'Filter the results where the student\'s change_dt matches the provided datetime.', type: 'datetime')]
    #[QueryParameter('filter[declare_dt]', description: 'Filter the results where the student\'s declare_dt matches the provided datetime.', type: 'datetime')]
    #[QueryParameter('filter[graduation_dt]', description: 'Filter the results where the student\'s graduation_dt matches the provided datetime.', type: 'datetime')]
    #[QueryParameter('filter[conferred_dt]', description: 'Filter the results where the student\'s conferred_dt matches the provided datetime.', type: 'datetime')]
    #[QueryParameter('page[number]', description: 'Control which page of students\'s programs is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many students\'s programs are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of students\'s programs that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'sisid', examples: [
        'sisid' => new Example('sisid'),
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
        'graduation_dt' => new Example('graduation_dt'),
        'conferred_dt' => new Example('conferred_dt'),
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
                AllowedFilter::partial('acad_plan'),
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
