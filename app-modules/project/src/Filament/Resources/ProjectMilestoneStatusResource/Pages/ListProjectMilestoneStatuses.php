<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource;
use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ListProjectMilestoneStatuses extends ListRecords
{
    protected static string $resource = ProjectMilestoneStatusResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('project_milestones_count')
                    ->label('Usage Count')
                    ->counts('milestones')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $deletedCount = ProjectMilestoneStatus::query()
                                ->whereKey($records)
                                ->whereDoesntHave('milestones')
                                ->delete();

                            Notification::make()
                                ->title('Deleted ' . $deletedCount . ' statuses')
                                ->body(
                                    $deletedCount < $records->count()
                                        ? ($records->count() - $deletedCount) . ' statuses were not deleted because they have associated project milestones.'
                                        : null
                                )
                                ->success()
                                ->send();
                        })
                        ->fetchSelectedRecords(false),
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
