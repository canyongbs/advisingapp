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

namespace AdvisingApp\Authorization\Filament\Resources\Roles\Pages\Concerns;

use App\Models\Authenticatable;
use App\Models\User;
use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait HasGuardAwareRoleUsers
{
    /**
     * @return Builder<Authenticatable>
     */
    protected function getRoleUsersQuery(string $guardName, ?string $search = null): Builder
    {
        $guardName = filled($guardName) ? $guardName : 'web';

        $modelClass = getModelForGuard($guardName);

        assert(is_string($modelClass));
        assert(is_a($modelClass, Authenticatable::class, true));

        $query = $modelClass::query()->orderBy('name');

        if (blank($search)) {
            return $query;
        }

        $normalizedSearch = Str::lower($search);

        $query->where(function (Builder $query) use ($guardName, $normalizedSearch): void {
            $query->where(new Expression('lower(name)'), 'like', "%{$normalizedSearch}%");

            if ($this->canSearchUsersByEmail($guardName)) {
                $query->orWhere(new Expression('lower(email)'), 'like', "%{$normalizedSearch}%");
            }
        });

        return $query;
    }

    protected function getRoleUsersValidationRule(Get $get): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($get): void {
            $userIds = Collection::make((array) $value)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($userIds === []) {
                return;
            }

            $resolvedCount = $this
                ->getRoleUsersQuery((string) $get('guard_name'))
                ->whereKey($userIds)
                ->count();

            if ($resolvedCount !== count($userIds)) {
                $fail('One or more selected users are invalid for the selected access control.');
            }
        };
    }

    protected function canSearchUsersByEmail(string $guardName): bool
    {
        $modelClass = getModelForGuard($guardName);

        return $modelClass === User::class;
    }

    protected function canAssignUserGroupsToRole(string $guardName, ?string $roleName): bool
    {
        if ($guardName !== 'web') {
            return false;
        }

        if (blank($roleName)) {
            return true;
        }

        $normalizedRoleName = mb_strtolower($roleName);
        $normalizedAdminRoleNames = collect(Authenticatable::ADMIN_ROLES)
            ->map(fn (string $name): string => mb_strtolower($name))
            ->all();

        return ! in_array($normalizedRoleName, $normalizedAdminRoleNames, true);
    }

    /**
     * @param  array<mixed>  $userGroupIds
     */
    protected function ensureUserGroupsCanBeAssigned(string $guardName, ?string $roleName, array $userGroupIds): void
    {
        $hasSelectedUserGroups = Collection::make($userGroupIds)
            ->filter()
            ->isNotEmpty();

        if (! $hasSelectedUserGroups) {
            return;
        }

        if ($this->canAssignUserGroupsToRole($guardName, $roleName)) {
            return;
        }

        throw ValidationException::withMessages([
            'userGroups' => 'Groups may only be assigned to non-admin Console roles.',
            'data.userGroups' => 'Groups may only be assigned to non-admin Console roles.',
        ]);
    }
}
