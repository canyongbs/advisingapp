<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\Concerns;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Scopes\MatchedToTermNamed;
use AdvisingApp\StudentDataModel\Models\Scopes\OrderByMostRecentTerm;
use AdvisingApp\StudentDataModel\Models\Scopes\WithoutMatchedTerm;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Features\TermsConsolidationFeature;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait SemesterSelectForOperator
{
    /**
     * Selects enrollments that were not matched to any term.
     */
    public const string UNASSIGNED_SEMESTER = '__unassigned__';

    public function getFormSchema(): array
    {
        return [
            ...parent::getFormSchema(),
            $this->semesterSelect(),
        ];
    }

    public static function semesterSelect(): Select
    {
        if (TermsConsolidationFeature::active()) {
            return Select::make('semesters')
                ->label('Semester')
                ->options(fn (): array => static::getTermSemesterOptions())
                ->searchable()
                ->multiple();
        }

        return Select::make('semesters')
            ->label('Semester')
            ->options(fn (): array => Enrollment::query()
                ->whereNotNull('semester_name')
                ->distinct()
                ->orderBy('semester_name')
                ->limit(50)
                ->pluck('semester_name', 'semester_name')
                ->all())
            ->getSearchResultsUsing(fn (string $search): array => Enrollment::query()
                ->whereNotNull('semester_name')
                ->where(DB::raw('lower(semester_name)'), 'like', '%' . Str::lower($search) . '%')
                ->distinct()
                ->orderBy('semester_name')
                ->limit(50)
                ->pluck('semester_name', 'semester_name')
                ->all())
            ->getOptionLabelsUsing(fn (array $values): array => array_combine($values, $values))
            ->searchable()
            ->multiple();
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

        if (TermsConsolidationFeature::active()) {
            return $query->whereHas(
                $relationshipName,
                fn (Builder $query) => static::applyTermSemestersToEnrollmentsQuery($query, $semesters),
                $this->getQueryOperator(),
                $count,
            );
        }

        $lowerSemesters = array_map(mb_strtolower(...), $semesters);

        return $query->whereHas($relationshipName, function (Builder $query) use ($lowerSemesters): void {
            $query->whereIn(DB::raw('lower(semester_name)'), $lowerSemesters);
        }, $this->getQueryOperator(), $count);
    }

    public function getSummary(): string
    {
        $summary = parent::getSummary();

        if (! empty($this->settings['semesters'])) {
            $semesters = array_map(
                fn (mixed $semester): mixed => $semester === static::UNASSIGNED_SEMESTER ? 'Unassigned' : $semester,
                Arr::wrap($this->settings['semesters']),
            );

            $concatenatedSemesters = implode(', ', $semesters);
            $summary .= ' in ' . (count($semesters) > 1 ? 'semesters ' : 'semester ') . $concatenatedSemesters;
        }

        return $summary;
    }

    /**
     * The names of the terms, most recent first, with names differing only in case listed once.
     *
     * @return array<int|string, string>
     */
    protected static function getTermSemesterOptions(): array
    {
        // A union rather than a spread, which would renumber numeric names such as `2024`.
        return Term::query()
            // TODO: Cleanup Task (terms-consolidation) Remove once `Term` uses `SoftDeletes`.
            ->whereNull('deleted_at')
            ->tap(new OrderByMostRecentTerm())
            ->pluck('name')
            ->unique(fn (string $name): string => mb_strtolower($name), strict: true)
            ->mapWithKeys(fn (string $name): array => [$name => $name])
            ->all() + [static::UNASSIGNED_SEMESTER => 'Unassigned'];
    }

    /**
     * Matches enrollments by the name of their matched term, ignoring case, or by having no matched term.
     *
     * @param Builder<Model> $query
     * @param array<string> $semesters
     */
    protected static function applyTermSemestersToEnrollmentsQuery(Builder $query, array $semesters): void
    {
        $termNames = array_values(array_diff($semesters, [static::UNASSIGNED_SEMESTER]));

        $query->where(fn (Builder $query) => $query
            ->when(
                filled($termNames),
                fn (Builder $query) => $query->where(fn (Builder $query) => $query->tap(new MatchedToTermNamed($termNames))),
            )
            ->when(
                in_array(static::UNASSIGNED_SEMESTER, $semesters, strict: true),
                fn (Builder $query) => $query->orWhere(fn (Builder $query) => $query->tap(new WithoutMatchedTerm())),
            ));
    }
}
