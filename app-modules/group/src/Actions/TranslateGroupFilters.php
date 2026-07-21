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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use Filament\QueryBuilder\Models\Scopes\QueryBuilderScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TranslateGroupFilters
{
    /**
     * Resolve the members of a group as an Eloquent query.
     *
     * @return Builder<Model>
     */
    public function execute(Group | string $group): Builder
    {
        $group = $this->resolveGroup($group);

        return $this->applyFilterToQuery($group, $group->model->query());
    }

    /**
     * Apply a group's population (dynamic QueryBuilder filters, or a static list of members)
     * onto an existing query.
     *
     * @template TModel of Model
     *
     * @param Builder<TModel> $query
     *
     * @return Builder<TModel>
     */
    public function applyFilterToQuery(Group | string $group, Builder $query): Builder
    {
        $group = $this->resolveGroup($group);

        if ($group->type === GroupType::Static) {
            return $query->whereKey($group->subjects()->pluck('subject_id'));
        }

        return $this->applyRawFiltersToQuery($group->model, $group->filters ?? [], $query);
    }

    /**
     * Resolve a query from an ad-hoc (unsaved) set of dynamic filters, using the same
     * QueryBuilder constraints as the Group builder for the given model.
     *
     * @param array<string, mixed> $filters
     *
     * @return Builder<Model>
     */
    public function executeRawFilters(GroupModel $model, array $filters): Builder
    {
        return $this->applyRawFiltersToQuery($model, $filters, $model->query());
    }

    /**
     * Apply an ad-hoc (unsaved) set of dynamic filters onto an existing query, using the
     * same QueryBuilder constraints as the Group builder for the given model.
     *
     * @template TModel of Model
     *
     * @param array<string, mixed> $filters
     * @param Builder<TModel> $query
     *
     * @return Builder<TModel>
     */
    public function applyRawFiltersToQuery(GroupModel $model, array $filters, Builder $query): Builder
    {
        $rules = data_get($filters, 'queryBuilder.rules', []);

        if (blank($rules)) {
            return $query;
        }

        return QueryBuilderScope::make($rules, $model->queryBuilderConstraints())($query);
    }

    private function resolveGroup(Group | string $group): Group
    {
        if ($group instanceof Group) {
            return $group;
        }

        return Group::query()->findOrFail($group);
    }
}
