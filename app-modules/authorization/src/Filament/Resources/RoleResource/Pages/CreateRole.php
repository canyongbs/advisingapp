<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Authorization\Filament\Resources\RoleResource\Pages;

use AdvisingApp\Authorization\Filament\Resources\RoleResource;
use AdvisingApp\Authorization\Models\PermissionGroup;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\Rules\Unique;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(125)
                    ->unique(
                        table: 'roles',
                        column: 'name',
                        modifyRuleUsing: function (Unique $rule, Get $get) {
                            $rule->where('guard_name', $get('guard_name'));
                        }
                    ),
                Select::make('guard_name')
                    ->required()
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->default('web')
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('permissions', [])),
                Textarea::make('description')
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                PermissionsMatrix::make('permissions')
                    ->columnSpanFull()
                    ->guard(fn (Get $get): string => $get('guard_name'))
                    ->visible(fn (Get $get): bool => filled($get('guard_name')))
                    ->permissionGroupModel(PermissionGroup::class),
            ]);
    }
}
