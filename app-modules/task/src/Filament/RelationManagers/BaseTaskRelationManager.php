<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Task\Filament\RelationManagers;

use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Filament\Resources\TaskResource\Components\TaskViewAction;
use AdvisingApp\Task\Models\Task;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTaskRelationManager extends ManageRelatedRecords
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(100)
                    ->string(),
                TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->string(),
                DateTimePicker::make('due')
                    ->label('Due Date')
                    ->native(false),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship(
                        'assignedTo',
                        'name',
                        fn (Builder $query) => $query->tap(new HasLicense($this->getOwnerRecord()->getLicenseType())),
                    )
                    ->nullable()
                    ->searchable(['name', 'email'])
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('created_at', 'desc')
            ->columns([
                IdColumn::make(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
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
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null)
                    ->sortable(),
                TextColumn::make('concern.full_name')
                    ->label('Related To')
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
                        fn ($query) => $query->where('assigned_to', auth()->id())
                    ),
                SelectFilter::make('assignedTo')
                    ->label('Assigned To')
                    ->relationship(
                        'assignedTo',
                        'name',
                        fn (Builder $query) => $query->tap(new HasLicense($this->getOwnerRecord()->getLicenseType())),
                    )
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $direction) => [$direction->value => \Livewire\str($direction->name)->title()->headline()]))
                    ->multiple()
                    ->default(
                        [
                            TaskStatus::Pending->value,
                            TaskStatus::InProgress->value,
                        ]
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize(function () {
                        $ownerRecord = $this->getOwnerRecord();

                        return auth()->user()->can('create', [Task::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
                    })
                    ->using(function (array $data, string $model): Model {
                        $data = collect($data);

                        /** @var Task $task */
                        $task = new ($model)($data->except('assigned_to')->toArray());

                        $task->assigned_to = $data->get('assigned_to');

                        $task->concern()->associate($this->getOwnerRecord());

                        $task->save();

                        return $task;
                    }),
            ])
            ->actions([
                TaskViewAction::make(),
                EditAction::make(),
                DissociateAction::make()
                    ->using(fn (Task $task) => $task->concern()->dissociate()->save()),
            ])
            ->recordUrl(null)
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->using(function (Collection $selectedRecords) {
                            $selectedRecords->each(
                                fn (Task $selectedRecord) => $selectedRecord->concern()->dissociate()->save()
                            );
                        }),
                ]),
            ]);
    }
}
