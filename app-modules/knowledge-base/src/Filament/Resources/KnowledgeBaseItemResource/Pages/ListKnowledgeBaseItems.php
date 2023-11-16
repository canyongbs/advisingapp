<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\TernaryFilter;
use App\Concerns\FilterTableWithOpenSearch;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use App\Filament\Columns\OpenSearch\TextColumn as OpenSearchTextColumn;
use App\Filament\Filters\OpenSearch\SelectFilter as OpenSearchSelectFilter;

class ListKnowledgeBaseItems extends ListRecords
{
    use FilterTableWithOpenSearch;

    protected static string $resource = KnowledgeBaseItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                OpenSearchTextColumn::make('question')
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
                OpenSearchTextColumn::make('public')
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
                OpenSearchSelectFilter::make('quality')
                    ->relationship('quality', 'name')
                    ->multiple()
                    ->preload(),
                OpenSearchSelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                OpenSearchSelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('public'),
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
            CreateAction::make(),
        ];
    }
}
