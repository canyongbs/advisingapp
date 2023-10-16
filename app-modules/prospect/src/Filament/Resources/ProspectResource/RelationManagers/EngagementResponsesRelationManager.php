<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\RelationManagers\RelationManager;

class EngagementResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'engagementResponses';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('content')
                    ->translateLabel(),
                TextEntry::make('sent_at')
                    ->dateTime('Y-m-d H:i:s'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                IdColumn::make(),
                TextColumn::make('content'),
                TextColumn::make('sent_at')
                    ->dateTime('Y-m-d H:i:s'),
            ])
            ->filters([
            ])
            ->headerActions([
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
