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
use AdvisingApp\Authorization\Models\UserGroup;
use App\Features\UserGroupsFeature;
use App\Models\Authenticatable;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class RoleUserGroupsSelect
{
    /**
     * Groups may only hold non-admin Console roles. Archived groups can't be newly selected, but stay listed
     * while they hold the role, so the grant can still be seen and removed.
     */
    public static function make(): Select
    {
        return Select::make('userGroups')
            ->label('Groups')
            ->relationship(
                'userGroups',
                'name',
                fn (Builder $query, ?Role $record): Builder => $query->where(fn (Builder $query): Builder => $query
                    ->withoutArchived() // @phpstan-ignore method.notFound
                    ->when($record, fn (Builder $query, Role $record): Builder => $query->orWhereHas(
                        'roles',
                        fn (Builder $query): Builder => $query->whereKey($record->getKey()),
                    ))),
            )
            ->searchable()
            ->multiple()
            ->preload()
            // TODO: Cleanup Task (user-groups-feature): once the flag is removed, drop the flag checks from the visibility and always hydrate the groups.
            ->visible(fn (Get $get, ?Role $record): bool => UserGroupsFeature::active()
                && (auth()->user()?->can('viewAny', UserGroup::class) ?? false)
                && (($record->guard_name ?? $get('guard_name')) === 'web')
                && (! in_array($record?->name, Authenticatable::ADMIN_ROLES, true)))
            ->loadStateFromRelationshipsUsing(function (Select $component): void {
                if (UserGroupsFeature::active()) {
                    $component->fillStateFromRelationship();
                }
            });
    }
}
