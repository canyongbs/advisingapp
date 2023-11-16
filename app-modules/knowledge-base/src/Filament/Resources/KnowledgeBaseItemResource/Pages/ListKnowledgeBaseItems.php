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
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
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
                OpenSearchTextColumn::make('quality_name')
                    ->label('Quality')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->quality->name),
                OpenSearchTextColumn::make('status_name')
                    ->label('Status')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->status->name),
                TextColumn::make('public')
                    ->label('Public')
                    ->translateLabel()
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                OpenSearchTextColumn::make('category_name')
                    ->label('Category')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->category->name),
            ])
            ->filters([
                OpenSearchSelectFilter::make('quality_id')
                    ->label('Quality')
                    ->relationship('quality', 'name')
                    ->multiple()
                    ->preload(),
                OpenSearchSelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                OpenSearchSelectFilter::make('category_id')
                    ->label('Category')
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
