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

namespace App\Filament\Concerns;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait SemesterSelectForOperator
{
    public function getFormSchema(): array
    {
        return [
            ...parent::getFormSchema(),
            $this->semesterSelect(),
        ];
    }

    public static function semesterSelect(): Select
    {
        return Select::make('semesters')
            ->label('Semester')
            ->options(static::getSemesterOptions())
            ->searchable()
            ->multiple();
    }

    /**
     * @return array<string, string>
     */
    public static function getSemesterOptions(): array
    {
        return Enrollment::query()
            ->whereNotNull('semester_name')
            ->distinct()
            ->orderBy('semester_name')
            ->pluck('semester_name', 'semester_name')
            ->all();
    }

    /**
     * @param  Builder<Model>  $query
     *
     * @return Builder<Model>
     */
    public function applyToBaseQuery(Builder $query): Builder
    {
        $relationshipName = $this->constraint->getRelationshipName();
        $count = $this->settings['count'] ?? 1;
        $semesters = $this->settings['semesters'] ?? null;

        if (blank($semesters)) {
            return parent::applyToBaseQuery($query);
        }

        $semesters = Arr::wrap($semesters);

        $semesters = array_values(array_filter($semesters, filled(...)));
        $lowerSemesters = array_map(mb_strtolower(...), $semesters);

        return $query->whereHas($relationshipName, function (Builder $query) use ($lowerSemesters) {
            $query->whereIn(DB::raw('LOWER(semester_name)'), $lowerSemesters);
        }, $this->getQueryOperator(), $count);
    }

    public function getSummary(): string
    {
        $summary = parent::getSummary();

        if (! empty($this->settings['semesters'])) {
            $semesters = Arr::wrap($this->settings['semesters']);

            $concatenatedSemesters = implode(', ', $semesters);
            $summary .= ' in "' . (count($semesters) > 1 ? 'semesters ' : 'semester ') . $concatenatedSemesters . '"';
        }

        return $summary;
    }
}
