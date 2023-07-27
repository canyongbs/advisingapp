<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;

class RoleGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'roleGroups';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->recordTitle(function ($record) {
                    return Str::of($record->name);
                }),
            ])
            ->actions([
                DetachAction::make()->label(function () {
                    return 'Remove Role Group';
                })
                    ->requiresConfirmation()
                    ->modalDescription(function ($record) {
                    }),
            ])
            ->bulkActions([
            ]);
    }
}
