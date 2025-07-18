<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEnrollments;

use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentEnrollmentResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListStudentEnrollmentsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<StudentEnrollmentResource>>
     */
    #[Group('Students')]
    #[QueryParameter('filter[division]', description: 'Filter the results where the student\'s Division contains the provided string.', type: 'string')]
    #[QueryParameter('filter[class_nbr]', description: 'Filter the results where the student\'s Class NBR contains the provided string.', type: 'string')]
    #[QueryParameter('filter[crse_grade_off]', description: 'Filter the results where the student\'s CRSE grade off contains the provided string.', type: 'string')]
    #[QueryParameter(
        'filter[unt_taken]',
        description: 'Filter results based on the student\'s UNT taken. Supports operators like =, <, <=, >, >=. Example: filter[unt_taken]>=15',
        type: 'integer'
    )]
    #[QueryParameter(
        'filter[unt_earned]',
        description: 'Filter results based on the student\'s UNT earned. Supports operators like =, <, <=, >, >=. Example: filter[unt_earned]>=5',
        type: 'integer'
    )]
    #[QueryParameter(
        'filter[last_upd_dt_stmp]',
        description: 'Filter results based on the student\'s Last UPD date STMP. Supports operators like =, <, <=, >, >=. Example: filter[last_upd_dt_stmp]>=2025-03-29',
        type: 'datetime'
    )]
    #[QueryParameter('filter[section]', description: 'Filter the results where the student\'s Section contains the provided string.', type: 'string')]
    #[QueryParameter('filter[name]', description: 'Filter the results where the student\'s Name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[department]', description: 'Filter the results where the student\'s Department contains the provided string.', type: 'string')]
    #[QueryParameter('filter[faculty_name]', description: 'Filter the results where the student\'s Faculty name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[faculty_email]', description: 'Filter the results where the student\'s Faculty email contains the provided string.', type: 'string')]
    #[QueryParameter('filter[semester_code]', description: 'Filter the results where the student\'s Semester code contains the provided string.', type: 'string')]
    #[QueryParameter('filter[semester_name]', description: 'Filter the results where the student\'s Semester name contains the provided string.', type: 'string')]
    #[QueryParameter(
        'filter[start_date]',
        description: 'Filter results based on the student\'s start date. Supports operators like =, <, <=, >, >=. Example: filter[start_date]>=2025-04-18',
        type: 'datetime'
    )]
    #[QueryParameter(
        'filter[end_date]',
        description: 'Filter results based on the student\'s end date. Supports operators like =, <, <=, >, >=. Example: filter[end_date]<=2025-12-31',
        type: 'datetime'
    )]
    #[QueryParameter('sort', description: 'Control the order of students\'s enrollments that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'sisid', examples: [
        'division' => new Example('division'),
        'class_nbr' => new Example('class_nbr'),
        'crse_grade_off' => new Example('crse_grade_off'),
        'unt_taken' => new Example('unt_taken'),
        'unt_earned' => new Example('unt_earned'),
        'last_upd_dt_stmp' => new Example('last_upd_dt_stmp'),
        'section' => new Example('section'),
        'name' => new Example('name'),
        'department' => new Example('department'),
        'faculty_name' => new Example('faculty_name'),
        'faculty_email' => new Example('faculty_email'),
        'semester_code' => new Example('semester_code'),
        'semester_name' => new Example('semester_name'),
        'start_date' => new Example('start_date'),
        'end_date' => new Example('end_date'),
    ])]
    public function __invoke(Request $request, Student $student): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('view', $student);
        Gate::authorize('viewAny', Enrollment::class);

        return QueryBuilder::for($student->enrollments())
            ->allowedFilters([
                AllowedFilter::partial('division'),
                AllowedFilter::partial('class_nbr'),
                AllowedFilter::partial('crse_grade_off'),
                AllowedFilter::operator('unt_taken', FilterOperator::DYNAMIC),
                AllowedFilter::operator('unt_earned', FilterOperator::DYNAMIC),
                AllowedFilter::operator('last_upd_dt_stmp', FilterOperator::DYNAMIC),
                AllowedFilter::partial('section'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('department'),
                AllowedFilter::partial('faculty_name'),
                AllowedFilter::partial('faculty_email'),
                AllowedFilter::partial('semester_code'),
                AllowedFilter::partial('semester_name'),
                AllowedFilter::operator('start_date', FilterOperator::DYNAMIC),
                AllowedFilter::operator('end_date', FilterOperator::DYNAMIC),
            ])
            ->allowedSorts([
                AllowedSort::field('division'),
                AllowedSort::field('class_nbr'),
                AllowedSort::field('crse_grade_off'),
                AllowedSort::field('unt_taken'),
                AllowedSort::field('unt_earned'),
                AllowedSort::field('last_upd_dt_stmp'),
                AllowedSort::field('section'),
                AllowedSort::field('name'),
                AllowedSort::field('department'),
                AllowedSort::field('faculty_name'),
                AllowedSort::field('faculty_email'),
                AllowedSort::field('semester_code'),
                AllowedSort::field('semester_name'),
                AllowedSort::field('start_date'),
                AllowedSort::field('end_date'),
            ])
            ->defaultSort(AllowedSort::field('sisid'))
            ->jsonPaginate()
            ->toResourceCollection(StudentEnrollmentResource::class);
    }
}
