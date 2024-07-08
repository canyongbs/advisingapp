<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class ProgramRelationManager extends RelationManager
{
    protected static string $relationship = 'basicNeedsPrograms';

    protected static ?string $title = 'Programs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable(),
                TextColumn::make('contact_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('contact_phone')
                    ->label('Phone')
                    ->searchable(),
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Programs Found.');
    }
}
