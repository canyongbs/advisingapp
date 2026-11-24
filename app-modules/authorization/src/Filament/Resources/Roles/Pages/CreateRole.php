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

namespace AdvisingApp\Authorization\Filament\Resources\Roles\Pages;

use AdvisingApp\Authorization\Enums\PermissionDescription;
use AdvisingApp\Authorization\Filament\Resources\Roles\RoleResource;
use AdvisingApp\Authorization\Models\PermissionGroup;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Enums\Feature;
use App\Features\UserGroupsFeature;
use App\Filament\Forms\Components\UserSelect;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Properties')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(125)
                            ->unique(
                                table: 'roles',
                                column: 'name',
                                modifyRuleUsing: function (Unique $rule, Get $get): void {
                                    $rule->where('guard_name', $get('guard_name'));
                                }
                            ),
                        ToggleButtons::make('guard_name')
                            ->label('Access Control')
                            ->required()
                            ->options([
                                'web' => 'Console',
                                'api' => 'API',
                            ])
                            ->inline()
                            ->default('web')
                            ->live()
                            ->afterStateUpdated(fn (Set $set): mixed => $set('permissions', [])),
                        Textarea::make('description')
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Section::make('Membership')
                    ->columns(2)
                    ->schema([
                        UserSelect::make('users')
                            ->relationship('users', 'name')
                            ->preload()
                            ->label('Users')
                            ->multiple()
                            ->exists('users', 'id'),
                        Select::make('userGroups')
                            ->relationship(
                                'userGroups',
                                'name',
                                fn (Builder $query) => $query
                                // @phpstan-ignore method.notFound
                                    ->withoutArchived()
                            )
                            ->preload()
                            ->searchable()
                            ->label('Groups')
                            ->multiple()
                            ->exists((new UserGroup())->getTable(), 'id')
                                // TODO: Cleanup Task (user-groups-feature): remove this visibility branch when User Groups is made permanent.
                            ->visible(fn (): bool => UserGroupsFeature::active()),
                    ]),
                PermissionsMatrix::make('permissions')
                    ->columnSpanFull()
                    ->guard(fn (Get $get): string => $get('guard_name'))
                    ->visible(fn (Get $get): bool => filled($get('guard_name')))
                    ->descriptions(PermissionDescription::cases())
                    ->permissionGroupModel(PermissionGroup::class)
                    ->hiddenPermissionGroups(fn (): array => Feature::getDisabledPermissionGroupNames()),
            ]);
    }
}
