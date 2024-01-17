<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;

class ListPromptTypes extends ListRecords
{
    protected static string $resource = PromptTypeResource::class;

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
                TextColumn::make('prompts_count')
                    ->label('# of Prompts')
                    ->counts('prompts')
                    ->sortable(),
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
