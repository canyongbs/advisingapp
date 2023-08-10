<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ListKnowledgeBaseItems extends ListRecords
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Question/Issue/Feature')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quality.name')
                    ->label('Quality')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('public')
                    ->label('Public')
                    ->translateLabel()
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->translateLabel()
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
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
            Actions\CreateAction::make(),
        ];
    }
}
