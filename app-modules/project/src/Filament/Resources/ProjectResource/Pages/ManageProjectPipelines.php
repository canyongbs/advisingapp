<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectResource\Pages;

use AdvisingApp\Pipeline\Filament\Resources\PipelineResource;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Project\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageProjectPipelines extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'pipelines';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Pipelines';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('segment.name'),
                TextColumn::make('createdBy.name')->label('Created By'),
            ])
            ->filters([
                Filter::make('createdBy')
                    ->label('My Pipelines')
                    ->default()
                    ->query(fn (Builder $query) => $query->where('user_id', auth()->id())),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(PipelineResource::getUrl('create', ['project' => $this->getRecord()->getKey()]))
                    ->authorize(fn (): bool => auth()->user()->can('create', Pipeline::class) && auth()->user()->can('update', $this->getRecord())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Pipeline $record): string => PipelineResource::getUrl('view', ['record' => $record])),
                Tables\Actions\EditAction::make()
                    ->url(fn (Pipeline $record): string => PipelineResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
