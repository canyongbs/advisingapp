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

namespace AdvisingApp\StudentDataModel\Filament\Filters;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Scopes\JoinMatchedTerm;
use AdvisingApp\StudentDataModel\Models\Scopes\OrderByFirstTerm;
use AdvisingApp\StudentDataModel\Models\Scopes\OrderByMostRecentTerm;
use AdvisingApp\StudentDataModel\Models\Term;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint\Operators\IsOperator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Filters students by the name of one of their enrolled terms (e.g. their first or most recent), picked from their enrollments' matched terms.
 *
 * Students whose enrollments are not matched to any term have no enrolled term, so they are neither included nor excluded by a term.
 * Values are stored lowercased, and compared ignoring case, so groups saved before terms were matched keep their format.
 */
class EnrolledTermConstraint
{
    /**
     * @param OrderByFirstTerm|OrderByMostRecentTerm $orderTerms Orders a student's matched terms so the enrolled term is first.
     */
    public static function make(string $name, OrderByFirstTerm|OrderByMostRecentTerm $orderTerms): SelectConstraint
    {
        return SelectConstraint::make($name)
            ->multiple()
            ->searchable()
            ->options(fn (): array => self::getTermNameOptions())
            ->operators([
                IsOperator::make()
                    ->baseQuery(function (Builder $query, array $settings, bool $isInverse) use ($orderTerms): Builder {
                        $termNames = array_values(array_filter($settings['values'] ?? [], is_string(...)));

                        if (blank($termNames)) {
                            return $query;
                        }

                        return $query->whereIn(
                            $query->qualifyColumn('sisid'),
                            self::getStudentsWithEnrolledTermQuery($orderTerms, $termNames, $isInverse),
                        );
                    }),
            ]);
    }

    /**
     * The names of the terms, most recent first, keyed by their lowercased name, with names differing only in case listed once.
     *
     * @return array<array-key, string>
     */
    private static function getTermNameOptions(): array
    {
        return Term::query()
            // TODO: Cleanup Task (terms-consolidation) Remove once `Term` uses `SoftDeletes`.
            ->whereNull('deleted_at')
            ->tap(new OrderByMostRecentTerm())
            ->pluck('name')
            ->unique(fn (string $name): string => mb_strtolower($name), strict: true)
            ->mapWithKeys(fn (string $name): array => [mb_strtolower($name) => $name])
            ->all();
    }

    /**
     * Picks each student's enrolled term for the whole tenant in one pass with `DISTINCT ON`, which is much faster than resolving it per student.
     *
     * @param array<string> $termNames
     */
    private static function getStudentsWithEnrolledTermQuery(OrderByFirstTerm|OrderByMostRecentTerm $orderTerms, array $termNames, bool $isInverse): QueryBuilder
    {
        $enrolledTerms = Enrollment::query()
            ->tap(new JoinMatchedTerm())
            ->distinct('enrollments.sisid')
            ->select(['enrollments.sisid', 'terms.name'])
            ->orderBy('enrollments.sisid')
            ->tap($orderTerms);

        // `terms.name` is `citext`, so the names are compared ignoring case.
        return DB::query()
            ->fromSub($enrolledTerms, 'enrolled_terms')
            ->select('enrolled_terms.sisid')
            ->{$isInverse ? 'whereNotIn' : 'whereIn'}('enrolled_terms.name', $termNames);
    }
}
