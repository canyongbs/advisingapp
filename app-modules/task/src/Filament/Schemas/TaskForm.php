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

namespace AdvisingApp\Task\Filament\Schemas;

use AdvisingApp\Authorization\Models\UserGroup;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use App\Features\UserGroupsFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Scopes\HasLicense;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TaskForm
{
    public static function configure(Schema $schema, Student | Prospect $owner): Schema
    {
        return $schema
            ->components([
                Section::make('Restrict Access')
                    ->schema([
                        Checkbox::make('is_confidential')
                            ->label('Confidential')
                            ->live(),
                        UserSelect::make('confidential_task_users')
                            ->relationship('confidentialAccessUsers', 'name')
                            ->preload()
                            ->label('Users')
                            ->multiple()
                            ->exists('users', 'id')
                            ->visible(fn (Get $get): mixed => $get('is_confidential')),
                        Select::make('confidential_task_departments')
                            ->relationship('confidentialAccessDepartments', 'name')
                            ->preload()
                            ->label('Departments')
                            ->multiple()
                            ->exists('teams', 'id')
                            ->visible(fn (Get $get): mixed => $get('is_confidential')),
                        Select::make('confidential_task_user_groups')
                            ->relationship(
                                'confidentialAccessUserGroups',
                                'name',
                                fn (Builder $query) => $query
                                // @phpstan-ignore method.notFound
                                    ->withoutArchived()
                            )
                            ->preload()
                            ->label('Groups')
                            ->multiple()
                            ->exists((new UserGroup())->getTable(), 'id')
                            // TODO: Cleanup Task (user-groups-feature): once the flag is removed, always show and hydrate group confidentiality selection when confidential.
                            ->visible(fn (Get $get): mixed => UserGroupsFeature::active() && $get('is_confidential'))
                            ->loadStateFromRelationshipsUsing(function (Select $component): void {
                                if (UserGroupsFeature::active()) {
                                    $component->fillStateFromRelationship();
                                }
                            }),
                    ]),
                Section::make('Properties')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(100)
                            ->string()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->required()
                            ->string()
                            ->columnSpanFull(),
                        DateTimePicker::make('due')
                            ->label('Due Date')
                            ->native(false),
                        Select::make('related_to')
                            ->label('Related To')
                            ->options([$owner->getKey() => $owner->{$owner::displayNameKey()}])
                            ->formatStateUsing(fn (): string => $owner->getKey())
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
                Section::make('Status')
                    ->schema([
                        ToggleButtons::make('status')
                            ->label('Task Status')
                            ->options(TaskStatus::class)
                            ->inline()
                            ->required()
                            ->default(TaskStatus::Pending),
                    ]),
                Section::make('Assignment')
                    ->schema([
                        UserSelect::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship(
                                'assignedTo',
                                'name',
                                fn (Builder $query): Builder => $query->tap(new HasLicense($owner->getLicenseType())),
                            )
                            ->nullable()
                            ->default(auth()->id()),
                    ]),
            ]);
    }
}
