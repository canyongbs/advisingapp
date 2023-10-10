<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use App\Models\User;
use Filament\Forms\Set;
use Filament\Tables\Table;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Assist\Task\Imports\TaskImporter;
use App\Filament\Actions\ImportAction;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Task\Filament\Resources\TaskResource;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Assist\Task\Filament\Resources\TaskResource\Components\TaskViewAction;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'task::filament.pages.list-tasks';

    public string $viewType = 'table';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                TextColumn::make('status')
                    ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                    ->badge()
                    ->color(fn (Task $record) => $record->status->getTableColor()),
                TextColumn::make('due')
                    ->label('Due Date')
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                    ->hidden(function (Table $table) {
                        return $table->getFilter('my_tasks')->getState()['isActive'];
                    }),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Task $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->educatableSearch(relationship: 'concern', search: $search))
                    ->url(fn (Task $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
            ])
            ->filters([
                Filter::make('my_tasks')
                    ->label('My Tasks')
                    ->query(
                        fn (Builder $query) => $query->where('assigned_to', auth()->id())
                    )
                    ->form([
                        Checkbox::make('isActive')
                            ->label('My Tasks')
                            ->afterStateUpdated(fn (Set $set) => $set('../my_teams_tasks.isActive', false))
                            ->default(true),
                    ]),
                Filter::make('my_teams_tasks')
                    ->label("My Team's Tasks")
                    ->query(
                        function (Builder $query) {
                            /** @var User $user */
                            $user = auth()->user();
                            //TODO: change this if we support multiple teams
                            $teamUserIds = $user->teams()->first()->users()->get()->pluck('id');

                            return $query->whereIn('assigned_to', $teamUserIds)->get();
                        }
                    )
                    ->form([
                        Checkbox::make('isActive')
                            ->label("My Team's Tasks")
                            ->afterStateUpdated(function (Set $set) {
                                return $set('../my_tasks.isActive', false);
                            }),
                    ]),
                SelectFilter::make('assignedTo')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction) => [$direction->value => \Livewire\str($direction->name)->title()->headline()]))
                    ->multiple()
                    ->default([
                        TaskStatus::PENDING->value,
                        TaskStatus::IN_PROGRESS->value,
                    ]),
            ])
            ->actions([
                TaskViewAction::make(),
                EditAction::make(),
            ])
            ->recordUrl(null)
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                TableCreateAction::make(),
            ]);
    }

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(TaskImporter::class)
                ->authorize('import', Task::class),
            CreateAction::make(),
        ];
    }
}
