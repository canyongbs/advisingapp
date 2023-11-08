<?php

namespace Assist\Division\Filament\Resources\DivisionResource\RelationManagers;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('division')
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
            ])
            ->filters([
            ])
            ->headerActions([
                AssociateAction::make(),
            ])
            ->actions([
                DissociateAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
            ]);
    }
}
