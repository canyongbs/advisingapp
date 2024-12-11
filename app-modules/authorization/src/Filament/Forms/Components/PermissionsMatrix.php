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

namespace AdvisingApp\Authorization\Filament\Forms\Components;

use Closure;
use Illuminate\Support\Str;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Authorization\Models\PermissionGroup;

class PermissionsMatrix extends Field
{
    protected string $view = 'authorization::filament.forms.components.permissions-matrix';

    protected string | Closure $guard;

    protected function setUp(): void
    {
        $this->default([]);

        $this->loadStateFromRelationshipsUsing(function (PermissionsMatrix $component, Model $record) {
            $component->state($record->permissions()->pluck('id')->all());
        });

        $this->saveRelationshipsUsing(function (Model $record, array $state) {
            $record->permissions()->sync($state);
        });

        $this->dehydrated(false);
    }

    public function guard(string | Closure $guard): static
    {
        $this->guard = $guard;

        return $this;
    }

    public function getGuard(): string
    {
        return $this->evaluate($this->guard);
    }

    public function getAvailablePermissions(): array
    {
        $permissions = PermissionGroup::query()
            ->with(['permissions' => fn (HasMany $query) => $query->where('guard_name', $this->getGuard())])
            ->get()
            ->reduce(function (array $carry, PermissionGroup $permissionGroup): array {
                $permissionGroupNameSlug = Str::slug($permissionGroup->name);

                $permissions = $permissionGroup->permissions
                    ->pluck('name', 'id')
                    ->all();

                if (! in_array("{$permissionGroupNameSlug}.view-any", $permissions)) {
                    return $carry;
                }

                foreach ($permissions as $permissionKey => $permissionName) {
                    $operation = match ($permissionName) {
                        "{$permissionGroupNameSlug}.view-any" => 'view-any',
                        "{$permissionGroupNameSlug}.*.view" => 'view',
                        "{$permissionGroupNameSlug}.create" => 'create',
                        "{$permissionGroupNameSlug}.*.update" => 'update',
                        "{$permissionGroupNameSlug}.*.delete" => 'delete',
                        "{$permissionGroupNameSlug}.import" => 'import',
                        "{$permissionGroupNameSlug}.*.force-delete" => 'force-delete',
                        "{$permissionGroupNameSlug}.*.restore" => 'restore',
                        default => null,
                    };

                    if (blank($operation)) {
                        continue;
                    }

                    $carry[$permissionGroup->name][$operation] = $permissionKey;
                }

                return $carry;
            }, initial: []);

        return $permissions;
    }
}
