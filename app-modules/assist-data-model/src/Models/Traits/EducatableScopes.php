<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
        $search = strtolower($search);

        return $query->whereHasMorph(
            $relationship,
            [Student::class, Prospect::class],
            function (Builder $query, string $type) use ($search) {
                $column = app($type)::displayNameKey();

                $query->where(
                    DB::raw("LOWER({$column})"),
                    'like',
                    "%{$search}%"
                );
            }
        );
    }
}
