<?php

namespace Assist\Authorization\Filament\Resources\RoleGroupResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
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
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()->recordTitle(function ($record) {
                    return Str::of($record->name)->append(' | ')->append($record->guard_name);
                }),
            ])
            ->actions([
                EditAction::make(),
                // TODO We'll want to modify the messages of the detach to make it more clear
                // To the end user they are also removing all of the roles from the user
                // That have been assigned to them through the RoleGroup
                DetachAction::make()->label(function () {
                    return 'Remove from ' . $this->ownerRecord->name . ' Role Group';
                }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
