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
    #[QueryParameter('filter[division]', description: 'Filter the results where the enrollment\'s Division contains the provided string.', type: 'string')]
    #[QueryParameter('filter[class_nbr]', description: 'Filter the results where the enrollment\'s Class NBR contains the provided string.', type: 'string')]
    #[QueryParameter('filter[crse_grade_off]', description: 'Filter the results where the enrollment\'s CRSE Grade Off contains the provided string.', type: 'string')]
    #[QueryParameter(
        'filter[unt_taken]',
        description: 'Filter results based on the enrollment\'s UNT taken. Supports operators like =, <, <=, >, >=. Example: filter[unt_taken]>=15',
        type: 'integer'
    )]
    #[QueryParameter(
        'filter[unt_earned]',
        description: 'Filter results based on the enrollment\'s UNT earned. Supports operators like =, <, <=, >, >=. Example: filter[unt_earned]>=5',
        type: 'integer'
    )]
    #[QueryParameter('filter[last_upd_dt_stmp]', description: 'Filter the results where the program\'s Last UPD Date STMP matches the provided date using comparison operators. Supported: =, <, <=, >, >=. Format: YYYY-MM-DD H:i:s.', type: 'datetime')]
    #[QueryParameter('filter[section]', description: 'Filter the results where the enrollment\'s Section contains the provided string.', type: 'string')]
    #[QueryParameter('filter[name]', description: 'Filter the results where the enrollment\'s Name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[department]', description: 'Filter the results where the enrollment\'s Department contains the provided string.', type: 'string')]
    #[QueryParameter('filter[faculty_name]', description: 'Filter the results where the enrollment\'s Faculty name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[faculty_email]', description: 'Filter the results where the enrollment\'s Faculty email contains the provided string.', type: 'string')]
    #[QueryParameter('filter[semester_code]', description: 'Filter the results where the enrollment\'s Semester code contains the provided string.', type: 'string')]
    #[QueryParameter('filter[semester_name]', description: 'Filter the results where the enrollment\'s Semester name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[start_date]', description: 'Filter the results where the program\'s Start Date matches the provided date using comparison operators. Supported: =, <, <=, >, >=. Format: YYYY-MM-DD H:i:s.', type: 'datetime')]
    #[QueryParameter('filter[end_date]', description: 'Filter the results where the program\'s End Date matches the provided date using comparison operators. Supported: =, <, <=, >, >=. Format: YYYY-MM-DD H:i:s.', type: 'datetime')]
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
