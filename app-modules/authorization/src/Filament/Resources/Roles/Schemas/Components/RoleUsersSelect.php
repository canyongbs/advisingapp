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

namespace AdvisingApp\Authorization\Filament\Resources\Roles\Schemas\Components;

use AdvisingApp\Authorization\Models\Role;
use App\Models\Scopes\ConditionalAdminScope;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RoleUsersSelect
{
    /**
     * A role's users are the authenticatables of its guard: users for Console roles and system users for API roles.
     * The options are queried by guard because, before the role exists, the relationship can't resolve its model.
     */
    public static function make(): Select
    {
        return Select::make('users')
            ->label('Users')
            ->relationship(
                'users',
                'name',
                static::hideAdminsTheUserCanNotSee(),
            )
            ->multiple()
            ->searchable()
            ->options(fn (Select $component, Get $get, ?Role $record): array => static::getUsersQuery($get, $record)
                ->limit($component->getOptionsLimit())
                ->pluck('name', 'id')
                ->all())
            ->getSearchResultsUsing(fn (Select $component, string $search, Get $get, ?Role $record): array => static::getUsersQuery($get, $record)
                ->where(function (Builder $query) use ($search): void {
                    $query->whereLike('name', "%{$search}%");

                    if ($query->getModel() instanceof User) {
                        $query->orWhereLike('email', "%{$search}%");
                    }
                })
                ->limit($component->getOptionsLimit())
                ->pluck('name', 'id')
                ->all())
            ->getOptionLabelsUsing(fn (array $values, Get $get, ?Role $record): array => static::getUsersQuery($get, $record)
                ->whereKey($values)
                ->pluck('name', 'id')
                ->all())
            ->visible(fn (Get $get, ?Role $record): bool => auth()->user()?->can('viewAny', static::getUsersModel($get, $record)) ?? false)
            ->loadStateFromRelationshipsUsing(function (Select $component): void {
                if ($component->isVisible()) {
                    $component->fillStateFromRelationship();
                }
            });
    }

    /**
     * @return class-string<Model>
     */
    protected static function getUsersModel(Get $get, ?Role $record): string
    {
        $model = getModelForGuard($record->guard_name ?? $get('guard_name'));

        assert(is_string($model) && is_a($model, Model::class, true));

        return $model;
    }

    /**
     * @return Builder<Model>
     */
    protected static function getUsersQuery(Get $get, ?Role $record): Builder
    {
        return static::getUsersModel($get, $record)::query()
            ->tap(static::hideAdminsTheUserCanNotSee())
            ->orderBy('name');
    }

    /**
     * Admins are hidden from the users who can't see them, as on the users list. Leaving them out of the state too
     * keeps them on the role when it is saved.
     */
    protected static function hideAdminsTheUserCanNotSee(): Closure
    {
        return fn (Builder $query): Builder => $query->when(
            $query->getModel() instanceof User,
            fn (Builder $query): Builder => $query->tap(new ConditionalAdminScope()),
        );
    }
}
