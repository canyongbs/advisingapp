<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectResource;
use App\Features\AssociateTasksWithProjectsFeature;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageTasks extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'tasks';

    public static function getNavigationLabel(): string
    {
        return 'Tasks';
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = auth()->user();

        return AssociateTasksWithProjectsFeature::active() && $user->can(['project.*.view']) && parent::canAccess($parameters);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->recordSelectOptionsQuery(fn(Builder $query) => $query->whereNull('project_id'))
                    ->preloadRecordSelect()
                    ->authorize(fn () => auth()->user()->can('update', $this->getOwnerRecord())),
            ])
            ->actions([
                DissociateAction::make()
                    ->authorize(fn () => auth()->user()->can('update', $this->getOwnerRecord())),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->authorize(fn () => auth()->user()->can('update', $this->getOwnerRecord())),
                ])
                ]);
    }
}
