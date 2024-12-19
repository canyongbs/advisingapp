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

namespace Database\Migrations\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait CanModifyPermissions
{
    /**
     * @param array<string, string> $names The keys of the array should be the permission names and the values should be the name of the group they belong to.
     */
    public function createPermissions(array $names, string $guardName): void
    {
        $groups = DB::table('permission_groups')
            ->pluck('id', 'name')
            ->all();

        [$newGroups, $groupsToCreate] = collect($names)
            ->values()
            ->unique()
            ->diff(array_keys($groups))
            ->reduce(function (array $carry, string $name) {
                $id = (string) Str::orderedUuid();

                $carry[0][$name] = $id;
                $carry[1][] = [
                    'id' => $id,
                    'name' => $name,
                    'created_at' => now(),
                ];

                return $carry;
            }, [[], []]);

        DB::table('permission_groups')
            ->insert($groupsToCreate);

        $groups = [
            ...$groups,
            ...$newGroups,
        ];

        DB::table('permissions')
            ->insertOrIgnore(array_map(function (string $name, string $groupName) use ($groups, $guardName): array {
                return [
                    'id' => (string) Str::orderedUuid(),
                    'group_id' => $groups[$groupName],
                    'guard_name' => $guardName,
                    'name' => $name,
                    'created_at' => now(),
                ];
            }, array_keys($names), array_values($names)));
    }

    /**
     * @param array<string> $names
     */
    public function deletePermissions(array $names, string $guardName): void
    {
        DB::table('permissions')
            ->where('guard_name', $guardName)
            ->whereIn('name', $names)
            ->delete();

        // Delete groups that no longer have any permissions
        DB::table('permission_groups')
            ->leftJoin('permissions', 'permission_groups.id', '=', 'permissions.group_id')
            ->whereNull('permissions.id')
            ->delete();
    }

    /**
     * @param array<string, string> $names
     */
    public function renamePermissions(array $names, string $guardName): void
    {
        collect($names)
            ->each(function (string $newName, string $oldName) use ($guardName) {
                DB::table('permissions')
                    ->where('guard_name', $guardName)
                    ->where('name', $oldName)
                    ->update([
                        'name' => $newName,
                    ]);
            });
    }

    /**
     * @param array<string, string> $groups
     */
    public function renamePermissionGroups(array $groups): void
    {
        collect($groups)
            ->each(function (string $newName, string $oldName) {
                DB::table('permission_groups')
                    ->where('name', $oldName)
                    ->update([
                        'name' => $newName,
                    ]);
            });
    }
}
