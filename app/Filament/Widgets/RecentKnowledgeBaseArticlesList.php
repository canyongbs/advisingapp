<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class RecentKnowledgeBaseArticlesList extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest KB Articles (5)')
            ->query(
                KnowledgeBaseItem::latest()->limit(5)
            )
            ->columns([
                IdColumn::make(),
                TextColumn::make('question')
                    ->label('Question/Issue/Feature')
                    ->searchable()
                    ->sortable()
                    ->limit()
                    ->tooltip(fn (KnowledgeBaseItem $record): string => $record->question),
                TextColumn::make('quality.name')
                    ->label('Quality')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('public')
                    ->label('Public')
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (KnowledgeBaseItem $record): string => KnowledgeBaseItemResource::getUrl(name: 'view', parameters: ['record' => $record])),
            ])
            ->recordUrl(
                fn (KnowledgeBaseItem $record): string => KnowledgeBaseItemResource::getUrl(name: 'view', parameters: ['record' => $record]),
            )
            ->paginated(false);
    }
}
