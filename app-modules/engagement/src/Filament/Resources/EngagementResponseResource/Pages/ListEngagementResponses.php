<?php

namespace Assist\Engagement\Filament\Resources\EngagementResponseResource\Pages;

use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Assist\Engagement\Filament\Resources\EngagementResponseResource;

class ListEngagementResponses extends ListRecords
{
    protected static string $resource = EngagementResponseResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->translateLabel(),
                TextColumn::make('content')
                    ->translateLabel(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ])
            ->emptyStateActions([
            ]);
    }
}
