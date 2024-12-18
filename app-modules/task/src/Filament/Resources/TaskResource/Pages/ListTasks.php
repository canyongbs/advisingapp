<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Task\Filament\Resources\TaskResource\Pages;

use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Scopes\EducatableSearch;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Filament\Resources\TaskResource;
use AdvisingApp\Task\Filament\Resources\TaskResource\Components\TaskViewAction;
use AdvisingApp\Task\Imports\TaskImporter;
use AdvisingApp\Task\Models\Task;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Set;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = TaskResource::class;

    protected static string $view = 'task::filament.pages.list-tasks';

    public ?string $viewType = null;

    public function mount(): void
    {
        parent::mount();

        $this->viewType = session('task-view-type') ?? 'table';
    }

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
                    ->badge(),
                TextColumn::make('due')
                    ->label('Due Date')
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                    ->hidden(function (Table $table) {
                        return $table->getFilter('my_tasks')->getState()['isActive'] ?? false;
                    }),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Task $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'concern', search: $search)))
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
                        TaskStatus::Pending->value,
                        TaskStatus::InProgress->value,
                    ])
                    ->visible(fn () => $this->viewType === 'table'),
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
            ]);
    }

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;

        session(['task-view-type' => $viewType]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('shared-filters')
                ->icon('heroicon-m-funnel')
                ->iconButton()
                ->badge(fn () => collect($this->table->getFilters())->map(fn (BaseFilter $filter) => $filter->getIndicators())->flatten()->count())
                ->form(fn () => $this->table->getFiltersForm())
                ->fillForm($this->tableFilters ?? [])
                ->modalSubmitAction(false)
                ->modalCancelAction(false),
            ImportAction::make()
                ->importer(TaskImporter::class)
                ->authorize('import', Task::class),
            CreateAction::make(),
        ];
    }
}
