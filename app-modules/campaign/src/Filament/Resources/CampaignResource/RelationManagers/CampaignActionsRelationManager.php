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
use Assist\Campaign\Models\CampaignAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class CampaignActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    public function form(Form $form): Form
    {
        /** @var CampaignAction $action */
        $action = $form->model;

        return $form
            ->schema([
                TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                ...$action->getEditFields(),
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
}
