<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns;

use Filament\Forms\Form;
use Filament\Tables\Table;
use AdvisingApp\Task\Models\Task;
use App\Models\Scopes\HasLicense;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use AdvisingApp\Task\Enums\TaskStatus;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DissociateAction;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Actions\DissociateBulkAction;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\Task\Filament\Resources\TaskResource\Components\TaskViewAction;

trait CanManageEducatableTasks
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
                        fn (Builder $query) => $query->tap(new HasLicense($this->getRecord()->getLicenseType())),
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
            ->columns([
                IdColumn::make(),
                TextColumn::make('description')
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
                    ->url(fn (Task $record) => $record->assignedTo ? UserResource::getUrl('view', ['record' => $record->assignedTo]) : null),
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
                        fn (Builder $query) => $query->tap(new HasLicense($this->getRecord()->getLicenseType())),
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
                        $ownerRecord = $this->getRecord();

                        return auth()->user()->can('create', [Task::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
                    })
                    ->using(function (array $data, string $model): Model {
                        $data = collect($data);

                        /** @var Task $task */
                        $task = new ($model)($data->except('assigned_to')->toArray());

                        $task->assigned_to = $data->get('assigned_to');

                        $task->concern()->associate($this->getRecord());

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
