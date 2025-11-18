<?php

namespace AdvisingApp\ResourceHub\Filament\Widgets;

use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ResourceHubArticleConcernsTable extends TableWidget
{
    public string $resourceHubArticleId;

    protected static ?string $heading = 'Concerns Raised';

    public function mount(string $resourceHubArticleId): void
    {
        $this->resourceHubArticleId = $resourceHubArticleId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ResourceHubArticleConcern::where('resource_hub_article_id', $this->resourceHubArticleId))
            ->columns([
                TextColumn::make('createdBy.name'),
                TextColumn::make('description'),
                TextColumn::make('created_at')
                    ->date(),
                TextColumn::make('status'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ConcernStatus::class),
            ]);
    }
}
