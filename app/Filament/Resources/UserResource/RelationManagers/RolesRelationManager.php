<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\RelationManagers\RelationManager;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('guard_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
                TextColumn::make('guard_name'),
                TextColumn::make('pivot.via')->label('Via'),
            ])
            ->filters([
            ])
            ->headerActions([
            ])
            ->actions([
                // As of 8/15/2023, we are currently removing the ability to detach an individual Role from a User.
                // All control will exist at the RoleGroup level.

                // FIXME There is currently a bug in Livewire/Filament that requires a refresh after adding a RoleGroup
                // Before deleting it. This is being looked into by the Filament team
                // DetachAction::make()->label(function () {
                //     return 'Remove Role';
                // })
                //     ->requiresConfirmation()
                //     ->modalIcon('heroicon-o-trash')
                //     ->color('danger')
                //     ->modalHeading(function ($record) {
                //         return "Remove {$record->name} Role?";
                //     })
                //     ->modalDescription(function ($record) {
                //         if ($record->via === ModelHasRolesViaEnum::Direct->value) {
                //             return 'Are you sure you would like to remove this role?';
                //         }

                //         if ($record->via === ModelHasRolesViaEnum::RoleGroup->value) {
                //             return 'By removing this role, you will also remove the user from the following Role Group(s):';
                //         }
                //     })
                //     ->modalContent(fn ($record): View => view(
                //         'filament.pages.users.confirm',
                //         ['record' => $record, 'roleGroups' => $record->roleGroups],
                //     ))
                //     ->after(function ($record) {
                //         RoleRemovedFromUser::dispatch($record, $this->ownerRecord);

                //         // TODO This is the "double action" that we will carry out to re-assign roles directly after removing a user from a RoleGroup
                //         // If that is the desired outcome of the administering user.
                //         // There are potentially other, more elegant ways to handle this as well. (wizard, form in modal with checkbox, etc..)
                //         // $this->mountAction(
                //         //     'afterDetach',
                //         //     [
                //         //         'record' => $record,
                //         //     ]
                //         // );
                //     }),
            ])
            ->bulkActions([
            ]);
    }

    // public function afterDetachAction(): Action
    // {
    //     return Action::make('afterDetach')
    //         ->requiresConfirmation()
    //         ->action(fn (array $arguments) => ray($arguments));
    // }
}
