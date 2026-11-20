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

use AdvisingApp\StudentDataModel\Models\Scopes\MatchedToTermNamed;
use AdvisingApp\StudentDataModel\Models\Scopes\OrderByMostRecentTerm;
use AdvisingApp\StudentDataModel\Models\Scopes\WithoutMatchedTerm;
use AdvisingApp\StudentDataModel\Models\Term;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait SemesterSelectForOperator
{
    /**
     * Selects enrollments that were not matched to any term.
     */
    public const string UNASSIGNED_SEMESTER = '__unassigned__';

    public function getFormSchema(): array
    {
        $schema = parent::getFormSchema();

        // A count of 0 selects students with no matching enrollments, or inverted, with any, so it is allowed unlike in Filament's operators.
        // Tables skip rules that fail validation, while saved groups apply them regardless, so this also keeps group previews and members the same.
        foreach ($schema as $component) {
            if (($component instanceof TextInput) && ($component->getName() === 'count')) {
                $component->minValue(0);
            }
        }

        return [
            ...$schema,
            static::semesterSelect(),
        ];
    }

    public static function semesterSelect(): Select
    {
        return Select::make('semesters')
            ->label('Semester')
            ->options(fn (): array => static::getTermSemesterOptions())
            ->searchable()
            ->multiple();
    }

    /**
     * Conditions that only depend on whether a student has any matching enrollments are checked for existence rather than by counting,
     * as counting runs a subquery for every student, which is far slower across a whole tenant.
     *
     * @param  Builder<Model>  $query
     *
     * @return Builder<Model>
     */
    public function applyToBaseQuery(Builder $query): Builder
    {
        $count = $this->getCountSetting();

        // Security: skip applying the constraint when the tampered setting is not numeric.
        if ($count === null) {
            return $query;
        }

        $relationshipName = $this->constraint->getRelationshipName();
        $operator = $this->getQueryOperator();
        $scopeEnrollments = $this->getEnrollmentsQueryScope();

        $matchesStudentsWithNone = match ($operator) {
            '>=' => 0 >= $count,
            '>' => 0 > $count,
            '<' => 0 < $count,
            '<=' => 0 <= $count,
            '=' => $count === 0,
            '!=' => $count !== 0,
            default => false,
        };

        $matchesAllStudentsWithSome = match ($operator) {
            '>=' => $count <= 1,
            '>', '!=' => $count < 1,
            default => false,
        };

        $matchesNoStudentsWithSome = match ($operator) {
            '<' => $count <= 1,
            '<=', '=' => $count < 1,
            default => false,
        };

        return match (true) {
            // A condition is still added, as an OR group with no conditions is dropped rather than matching everyone.
            $matchesStudentsWithNone && $matchesAllStudentsWithSome => $query->whereRaw('true'),
            $matchesAllStudentsWithSome => $query->whereHas($relationshipName, $scopeEnrollments),
            $matchesStudentsWithNone && $matchesNoStudentsWithSome => $query->whereDoesntHave($relationshipName, $scopeEnrollments),
            $matchesNoStudentsWithSome => $query->whereRaw('false'),
            default => $query->whereHas($relationshipName, $scopeEnrollments, $operator, $count),
        };
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
            ->tap(new OrderByMostRecentTerm())
            ->pluck('name')
            ->unique(fn (string $name): string => mb_strtolower($name), strict: true)
            ->mapWithKeys(fn (string $name): array => [$name => $name])
            ->all() + [static::UNASSIGNED_SEMESTER => 'Unassigned'];
    }

    /**
     * The number of matching enrollments compared with, or null when the setting is not numeric.
     */
    protected function getCountSetting(): ?int
    {
        $count = $this->getNumericSetting('count');

        return ($count === null) ? null : (int) $count;
    }

    /**
     * Narrows the enrollments to the selected semesters, if any.
     *
     * @return Closure(Builder<Model>): Builder<Model>
     */
    protected function getEnrollmentsQueryScope(): Closure
    {
        $semesters = array_values(array_filter(
            Arr::wrap($this->getSettings()['semesters'] ?? []),
            fn (mixed $semester): bool => is_string($semester) && filled($semester),
        ));

        $modifyRelationshipQueryUsing = $this->constraint->getModifyRelationshipQueryUsing();

        return function (Builder $query) use ($semesters, $modifyRelationshipQueryUsing): Builder {
            if ($modifyRelationshipQueryUsing) {
                $query = $this->evaluate($modifyRelationshipQueryUsing, [
                    'query' => $query,
                ]) ?? $query;
            }

            if (filled($semesters)) {
                static::applyTermSemestersToEnrollmentsQuery($query, $semesters);
            }

            return $query;
        };
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
