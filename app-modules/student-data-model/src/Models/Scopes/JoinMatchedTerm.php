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

namespace AdvisingApp\StudentDataModel\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Joins enrollments to the term matched to each by the `enrollment_terms` view, so `terms` columns can be selected, grouped and ordered by.
 *
 * Joining is fast across a whole tenant, as Postgres caches the view's lookup per distinct SIS term ID and semester name.
 * Resolving terms one enrollment at a time in a correlated subquery (e.g. `whereHas('term')`) is not, so prefer this.
 * Select `enrollments.*` when loading enrollments, so the joined columns do not overwrite theirs.
 */
class JoinMatchedTerm
{
    /**
     * @param bool $includeUnmatched Keep enrollments without a matched term, with null `terms` columns.
     * @param string $enrollmentsTable The name or alias of the enrollments table in the query.
     */
    public function __construct(
        private bool $includeUnmatched = false,
        private string $enrollmentsTable = 'enrollments',
    ) {}

    /**
     * @template TModel of Model
     *
     * @param Builder<TModel> $query
     */
    public function __invoke(Builder $query): void
    {
        $type = $this->includeUnmatched ? 'left' : 'inner';

        $query
            ->join('enrollment_terms', 'enrollment_terms.enrollment_id', '=', "{$this->enrollmentsTable}.id", $type)
            ->join('terms', 'terms.id', '=', 'enrollment_terms.term_id', $type);
    }
}
