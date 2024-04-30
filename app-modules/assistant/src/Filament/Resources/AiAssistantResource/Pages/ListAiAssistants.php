<?php

namespace AdvisingApp\Assistant\Filament\Resources\AiAssistantResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use AdvisingApp\Assistant\Filament\Resources\AiAssistantResource;

class ListAiAssistants extends ListRecords
{
    protected static string $resource = AiAssistantResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatar')
                    ->visibility('private'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->emptyStateHeading('No AI Assistants')
            ->emptyStateDescription('Add a new custom AI Assistant by clicking the "Create AI Assistant" button above.');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create AI Assistant'),
        ];
    }
}
