<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Assistant\Models\Prompt;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;

class ListPrompts extends ListRecords
{
    protected static string $resource = PromptResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('prompt')
                    ->limit(50)
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('type.title')
                    ->sortable()
                    ->url(fn (Prompt $record) => PromptTypeResource::getUrl('view', ['record' => $record->type])),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
