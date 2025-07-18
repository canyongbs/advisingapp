<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEnrollments;

use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentEnrollmentResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Database\Eloquent\Builder;
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
    public function __invoke(Request $request, Student $student): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('view', $student);
        Gate::authorize('viewAny', Enrollment::class);

        return QueryBuilder::for($student->enrollments())
            ->allowedFilters([
                // AllowedFilter::partial('sisid'),
                // AllowedFilter::partial('acad_career'),
                // AllowedFilter::partial('division'),
                // AllowedFilter::callback('acad_plan', function (Builder $query, string $value) {
                //     $query->whereRaw('acad_plan::text ILIKE ?', ["%{$value}%"]);
                // }),
                // AllowedFilter::partial('prog_status'),
                // AllowedFilter::operator('cum_gpa', FilterOperator::DYNAMIC),
                // AllowedFilter::partial('semester'),
                // AllowedFilter::partial('descr'),
                // AllowedFilter::partial('foi'),
                // AllowedFilter::operator('change_dt', FilterOperator::DYNAMIC),
                // AllowedFilter::operator('declare_dt', FilterOperator::DYNAMIC),
                // AllowedFilter::operator('graduation_dt', FilterOperator::DYNAMIC),
                // AllowedFilter::operator('conferred_dt', FilterOperator::DYNAMIC),
            ])
            ->allowedSorts([
                // AllowedSort::field('sisid'),
                // AllowedSort::field('acad_career'),
                // AllowedSort::field('division'),
                // AllowedSort::field('acad_plan'),
                // AllowedSort::field('prog_status'),
                // AllowedSort::field('cum_gpa'),
                // AllowedSort::field('semester'),
                // AllowedSort::field('descr'),
                // AllowedSort::field('foi'),
                // AllowedSort::field('change_dt'),
                // AllowedSort::field('declare_dt'),
                // AllowedSort::field('graduation_dt'),
                // AllowedSort::field('conferred_dt'),
            ])
            ->defaultSort(AllowedSort::field('sisid'))
            ->jsonPaginate()
            ->toResourceCollection(StudentEnrollmentResource::class);
    }
}
