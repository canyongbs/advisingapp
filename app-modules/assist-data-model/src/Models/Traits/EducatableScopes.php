<?php

namespace Assist\AssistDataModel\Models\Traits;

use Illuminate\Support\Facades\DB;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;

trait EducatableScopes
{
    public function scopeEducatableSort(Builder $query, string $direction): Builder
    {
        $studentNameColumn = Student::displayNameKey();

        $prospectNameColumn = Prospect::displayNameKey();

        return $query->leftJoin('students', function ($join) {
            $join->on('service_requests.respondent_type', '=', DB::raw("'student'"))
                ->on(DB::raw('service_requests.respondent_id::VARCHAR'), '=', 'students.sisid');
        })
            ->leftJoin('prospects', function ($join) {
                $join->on('service_requests.respondent_type', '=', DB::raw("'prospect'"))
                    ->on(DB::raw('CAST(service_requests.respondent_id AS VARCHAR)'), '=', DB::raw('CAST(prospects.id AS VARCHAR)'));
            })
            ->select('service_requests.*', DB::raw("COALESCE(students.{$studentNameColumn}, prospects.{$prospectNameColumn}) as respondent_name"))
            ->orderBy('respondent_name', $direction);
    }

    public function scopeEducatableSearch(Builder $query, string $relationship, string $search): Builder
    {
        return $query->whereHasMorph(
            $relationship,
            [Student::class, Prospect::class],
            fn (Builder $query, string $type) => $query->where(
                app($type)::displayNameKey(),
                'ilike',
                "%{$search}%"
            )
        );
    }
}
