<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\RelationManagers\RelationManager;

class CreatedByRelationManager extends RelationManager
{
    protected static string $relationship = 'createdBy';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (User $user) => UserResource::getUrl('view', ['record' => $user])),
            ]);
    }
}
