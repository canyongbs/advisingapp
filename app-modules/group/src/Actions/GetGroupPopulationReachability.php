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

namespace AdvisingApp\Group\Actions;

use AdvisingApp\Group\DataTransferObjects\GroupPopulationReachability;
use AdvisingApp\StudentDataModel\Models\Scopes\UnhealthyEducatablePrimaryEmailAddress;
use AdvisingApp\StudentDataModel\Models\Scopes\UnhealthyEducatablePrimaryPhoneNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GetGroupPopulationReachability
{
    /**
     * Counts a group's members, and those who can't be reached by email or SMS, in one query.
     *
     * A group's query can be slow across a whole tenant, so it runs once with filtered counts, rather than once per count.
     *
     * @template TModel of Model
     *
     * @param Builder<TModel> $query The group's members.
     */
    public function __invoke(Builder $query): GroupPopulationReachability
    {
        [$unableToEmailCondition, $unableToEmailBindings] = $this->compileCondition($query, new UnhealthyEducatablePrimaryEmailAddress());
        [$unableToTextCondition, $unableToTextBindings] = $this->compileCondition($query, new UnhealthyEducatablePrimaryPhoneNumber());

        $counts = $query->toBase()
            ->cloneWithout(['columns', 'orders', 'limit', 'offset'])
            ->cloneWithoutBindings(['select', 'order'])
            ->selectRaw('count(*) as total_count')
            ->selectRaw("count(*) filter (where {$unableToEmailCondition}) as unable_to_email_count", $unableToEmailBindings)
            ->selectRaw("count(*) filter (where {$unableToTextCondition}) as unable_to_text_count", $unableToTextBindings)
            ->first();

        assert(is_object($counts));

        return new GroupPopulationReachability(
            totalCount: (int) $counts->total_count,
            unableToEmailCount: (int) $counts->unable_to_email_count,
            unableToTextCount: (int) $counts->unable_to_text_count,
        );
    }

    /**
     * Compiles a scope's conditions on the members' table, so they can filter a count of the group's members.
     *
     * @template TModel of Model
     *
     * @param Builder<TModel> $query
     *
     * @return array{string, array<mixed>}
     */
    private function compileCondition(Builder $query, UnhealthyEducatablePrimaryEmailAddress | UnhealthyEducatablePrimaryPhoneNumber $scope): array
    {
        $condition = $query->getModel()
            ->newQueryWithoutScopes()
            ->where(fn (Builder $query): Builder => $scope($query))
            ->toBase();

        return [
            Str::after($condition->getGrammar()->compileWheres($condition), 'where '),
            $condition->getBindings(),
        ];
    }
}
