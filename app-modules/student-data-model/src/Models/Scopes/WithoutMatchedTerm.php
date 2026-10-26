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

use AdvisingApp\StudentDataModel\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Filters enrollments to those not matched to any term, the same as having no row in the `enrollment_terms` view.
 *
 * Resolving an enrollment's term through the view is slow across many enrollments, so this applies the
 * view's candidate conditions directly: no non-deleted term has the enrollment's SIS term ID or its semester name.
 */
class WithoutMatchedTerm
{
    /**
     * @template TModel of Model
     *
     * @param Builder<TModel> $query
     */
    public function __invoke(Builder $query): void
    {
        $query
            ->where(fn (Builder $query) => $query
                ->whereNull('enrollments.sis_term_id')
                ->orWhereNotIn(
                    'enrollments.sis_term_id',
                    Term::query()
                        // TODO: Cleanup Task (terms-consolidation) Remove once `Term` uses `SoftDeletes`.
                        ->whereNull('deleted_at')
                        ->whereNotNull('sis_term_id')
                        ->select('sis_term_id'),
                ))
            ->where(fn (Builder $query) => $query
                ->whereNull('enrollments.semester_name')
                ->orWhereNotIn(
                    DB::raw('enrollments.semester_name::citext'),
                    Term::query()
                        // TODO: Cleanup Task (terms-consolidation) Remove once `Term` uses `SoftDeletes`.
                        ->whereNull('deleted_at')
                        ->whereNotNull('name')
                        ->select('name'),
                ));
    }
}
