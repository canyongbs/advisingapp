<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization\Models\Concerns;

use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\PermissionRegistrar;
use App\Actions\Finders\ApplicationModules;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesWithPivot
{
    use HasRoles {
        HasRoles::assignRole as protected originalAssignRole;
    }

    public function roles(): BelongsToMany
    {
        $permissionRegistrar = app(PermissionRegistrar::class);

        $relation = $this->morphToMany(
            // TODO make a slightly better helper similar to the config helper that still allows
            // Us to pass an exact path to the key within the config, not just the path of the file
            resolve(ApplicationModules::class)->moduleConfig('authorization', 'permission')['models']['role'],
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            $permissionRegistrar->pivotRole
        );

        if (! $permissionRegistrar->teams) {
            return $relation;
        }

        return $relation->wherePivot($permissionRegistrar->teamsKey, getPermissionsTeamId())
            ->where(function ($q) use ($permissionRegistrar) {
                $teamField = config('permission.table_names.roles') . '.' . $permissionRegistrar->teamsKey;
                $q->whereNull($teamField)->orWhere($teamField, getPermissionsTeamId());
            });
    }
}
