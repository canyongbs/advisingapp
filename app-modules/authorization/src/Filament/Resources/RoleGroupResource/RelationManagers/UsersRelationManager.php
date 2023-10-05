<?php

namespace Assist\Authorization\Filament\Resources\RoleGroupResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'email';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
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
                TextColumn::make('email'),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                // TODO We'll want to modify the messages of the detach to make it more clear
                // To the end user they are also removing all of the roles from the user
                // That have been assigned to them through the RoleGroup
                DetachAction::make()->label(function () {
                    return 'Remove from ' . $this->ownerRecord->name . ' Role Group';
                }),
            ])
            ->bulkActions([
            ]);
    }
}
