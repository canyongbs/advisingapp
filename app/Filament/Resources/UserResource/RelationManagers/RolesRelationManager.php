<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\DetachAction;
use Assist\Authorization\Enums\ModelHasRolesViaEnum;
use Filament\Resources\RelationManagers\RelationManager;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('guard_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('guard_name'),
                Tables\Columns\TextColumn::make('pivot.via')->label('Via'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->recordTitle(function ($record) {
                    return Str::of($record->name)->append(' | ')->append($record->guard_name);
                }),
            ])
            ->actions([
                DetachAction::make()->label(function () {
                    return 'Remove Role';
                })
                    ->requiresConfirmation()
                    ->modalDescription(function ($record) {
                        if ($record->via === ModelHasRolesViaEnum::Direct->value) {
                            return 'Are you sure you would like to remove this role?';
                        }

                        if ($record->via === ModelHasRolesViaEnum::RoleGroup->value) {
                            // TODO need to find the role group for the role specified here...
                            // A role can belong to multiple role groups, so we effectively just need to find every role group that the role/user belongs to
                            // And find what matches here
                            return "By removing this role, you will also remove the user from the {$record} Role Group. Are you sure you want to do that?";
                        }

                        // TODO we also need a second piece of dialog that asks if the user wants to apply all of the other roles that belong to the RoleGroup
                        // The user is being removed from directly.
                    }),
            ])
            ->bulkActions([
            ]);
    }
}
