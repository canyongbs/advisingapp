<?php

namespace Assist\AssistDataModel\Models\Scopes;

use Illuminate\Support\Facades\DB;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;

class EducatableSort
{
    public function __construct(
        protected string $direction
    ) {}

    public function __invoke(Builder $query): void
    {
        $studentNameColumn = Student::displayNameKey();

        $prospectNameColumn = Prospect::displayNameKey();

        $query->leftJoin('students', function ($join) {
            $join->on('service_requests.respondent_type', '=', DB::raw("'student'"))
                ->on(DB::raw('service_requests.respondent_id::VARCHAR'), '=', 'students.sisid');
        })
            ->leftJoin('prospects', function ($join) {
                $join->on('service_requests.respondent_type', '=', DB::raw("'prospect'"))
                    ->on(DB::raw('CAST(service_requests.respondent_id AS VARCHAR)'), '=', DB::raw('CAST(prospects.id AS VARCHAR)'));
            })
            ->select('service_requests.*', DB::raw("COALESCE(students.{$studentNameColumn}, prospects.{$prospectNameColumn}) as respondent_name"))
            ->orderBy('respondent_name', $this->direction);
    }
}
