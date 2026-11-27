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
use App\Models\Authenticatable;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\Rules\Unique;

class RoleNameInput
{
    /**
     * Admin role names are reserved on every guard, as admin checks match a role by its name alone.
     */
    public static function make(): TextInput
    {
        return TextInput::make('name')
            ->required()
            ->maxLength(125)
            ->unique(
                ignoreRecord: true,
                modifyRuleUsing: fn (Unique $rule, Get $get, ?Role $record): Unique => $rule->where('guard_name', $record->guard_name ?? $get('guard_name')),
            )
            ->notIn(fn (?Role $record): array => array_values(array_diff(Authenticatable::ADMIN_ROLES, [$record?->name])))
            ->validationMessages([
                'not_in' => 'This name is reserved for an admin role.',
            ]);
    }
}
