<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Illuminate\View\View;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Assist\Audit\Filament\Actions\AuditAttachAction;
use Assist\Authorization\Enums\ModelHasRolesViaEnum;
use Assist\Authorization\Events\RoleRemovedFromUser;
use Filament\Resources\RelationManagers\RelationManager;

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
