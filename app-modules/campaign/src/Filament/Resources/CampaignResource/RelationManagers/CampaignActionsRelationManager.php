<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Assist\Campaign\Models\Campaign;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class CampaignActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // TODO This should not be changeable
                // This was created through an enum, and defines the fields that will be rendered below
                TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                ...$this->getEditFields(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('type'),
            ])
            ->filters([
            ])
            ->headerActions([
                // Disabled for now. We'll need to determine the rules we want to establish
                // with product, but theoretically this should only show if there are other
                // "types" of actions that this Campaign has not already defined available
                // CreateAction::make(),
            ])
            ->actions([
                EditAction::make()
                    ->hidden(fn (Campaign $ownerRecord) => $ownerRecord->hasBeenExecuted() === true),
                DeleteAction::make()
                    ->hidden(fn (Campaign $ownerRecord) => $ownerRecord->hasBeenExecuted() === true),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->hidden(fn (Campaign $ownerRecord) => $ownerRecord->hasBeenExecuted() === true),
            ]);
    }

    // TODO Remove this from here once we're able to get context of a record to edit from the Relation Manager
    public function getEditFields(): array
    {
        return [
            TextInput::make('data.subject')
                ->required()
                ->maxLength(255),
            TextInput::make('data.body')
                ->required()
                ->maxLength(255),
            TextInput::make('data.delivery_methods')
                ->required()
                ->maxLength(255),
        ];
    }
}
