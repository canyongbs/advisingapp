<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Task\Filament\Tables;

use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Filament\Resources\Tasks\Components\TaskViewAction;
use AdvisingApp\Task\Models\Task;
use App\Features\TaskArchivingFeature;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use CanyonGBS\Common\Filament\Actions\ArchiveBulkAction;
use CanyonGBS\Common\Filament\Actions\UnarchiveAction;
use CanyonGBS\Common\Filament\Actions\UnarchiveBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * The table shared by the Student and Prospect task pages.
 *
 * Clicking a row opens the edit or view slide-over, so those actions stay registered for the row click to mount them, but their row buttons are not shown.
 */
class TaskTable
{
    public static function configure(Table $table, Student | Prospect $owner): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('created_at', 'desc')
            ->columns([
                IdColumn::make(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(50)
                    ->icon(fn (Task $record): ?string => $record->is_confidential ? 'heroicon-m-lock-closed' : null)
                    ->tooltip(fn (Task $record): ?string => $record->is_confidential ? 'Confidential' : null),
                TextColumn::make('status')
                    ->formatStateUsing(fn (TaskStatus $state): string => str($state->value)->title()->headline())
                    ->badge()
                    ->sortable(),
                TextColumn::make('due')
                    ->label('Due Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->url(fn (Task $record): ?string => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                    ->sortable(),
                TextColumn::make('concern.full_name')
                    ->label('Related To')
                    ->url(fn (Task $record): ?string => match ($record->concern ? $record->concern::class : null) {
                        Student::class => StudentResource::getViewUrl($record->concern),
                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->concern]),
                        default => null,
                    }),
                IconColumn::make('archived')
                    ->label('Archived')
                    ->boolean()
                    ->state(fn (Task $record): bool => $record->isArchived())
                    ->hidden(function (Table $table): bool {
                        $filter = $table->getFilter('withoutArchived');

                        if ((! TaskArchivingFeature::active()) || (! $filter)) {
                            return true;
                        }

                        // The column manager is initialized before the filter defaults are filled and query string values
                        // are normalized on the first request, so resolve the visibility from the raw or default state.
                        return filter_var($filter->getState()['isActive'] ?? $filter->getDefaultState(), FILTER_VALIDATE_BOOLEAN);
                    }),
            ])
            ->filters([
                Filter::make('my_tasks')
                    ->label('My Tasks')
                    ->query(
                        fn (Builder $query): Builder => $query->where('assigned_to', auth()->id())
                    ),
                SelectFilter::make('assignedTo')
                    ->label('Assigned To')
                    ->relationship(
                        'assignedTo',
                        'name',
                        fn (Builder $query): Builder => $query->tap(new HasLicense($owner->getLicenseType())),
                    )
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction): array => [$direction->value => str($direction->name)->title()->headline()]))
                    ->multiple()
                    ->default(
                        [
                            TaskStatus::Pending->value,
                            TaskStatus::InProgress->value,
                        ]
                    ),
                ...(TaskArchivingFeature::active() ? [
                    Filter::make('withoutArchived')
                        ->label('Without archived')
                        ->query(fn (Builder $query) => $query->withoutArchived()) // @phpstan-ignore method.notFound
                        ->default(),
                ] : []),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->authorize(fn (): bool => auth()->user()->can('create', [Task::class, $owner instanceof Prospect ? $owner : null]))
                    ->using(function (array $data) use ($owner): Task {
                        $task = new Task(Arr::except($data, ['assigned_to', 'status']));

                        $task->assigned_to = $data['assigned_to'] ?? null;
                        $task->status = static::resolveStatus($data['status'] ?? null) ?? TaskStatus::Pending;

                        $task->concern()->associate($owner);

                        $task->save();

                        return $task;
                    }),
            ])
            ->recordActions([
                TaskViewAction::make()
                    ->extraAttributes(['class' => 'hidden']),
                EditAction::make()
                    ->slideOver()
                    ->using(function (Task $record, array $data): Task {
                        $record->fill(Arr::except($data, ['assigned_to', 'status']));

                        $record->assigned_to = $data['assigned_to'] ?? null;

                        $status = static::resolveStatus($data['status'] ?? null);

                        if ($status && ($status !== $record->status)) {
                            // Transitioning saves the task, including the other changes, so the update is recorded once.
                            $record->getStateMachine('status')->transitionTo($status);
                        } else {
                            $record->save();
                        }

                        return $record;
                    })
                    ->extraModalFooterActions([
                        ArchiveAction::make()
                            ->visible(fn (): bool => TaskArchivingFeature::active())
                            ->cancelParentActions(),
                        UnarchiveAction::make()
                            ->visible(fn (): bool => TaskArchivingFeature::active())
                            ->cancelParentActions(),
                    ])
                    ->extraAttributes(['class' => 'hidden']),
            ])
            ->recordAction(fn (Task $record): string => auth()->user()->can('update', $record) ? 'edit' : 'view')
            ->recordUrl(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    ArchiveBulkAction::make()
                        ->authorizeIndividualRecords('delete')
                        ->visible(fn (): bool => TaskArchivingFeature::active()),
                    UnarchiveBulkAction::make()
                        ->authorizeIndividualRecords('delete')
                        ->visible(fn (): bool => TaskArchivingFeature::active()),
                ]),
            ]);
    }

    protected static function resolveStatus(TaskStatus | string | null $status): ?TaskStatus
    {
        if ($status instanceof TaskStatus) {
            return $status;
        }

        return filled($status) ? TaskStatus::from($status) : null;
    }
}
