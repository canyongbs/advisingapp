<?php

namespace Assist\Task\Filament\Pages;

use Exception;
use Filament\Pages\Page;
use Assist\Task\Models\Task;
use Filament\Actions\EditAction;
use Assist\Task\Enums\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Actions\Concerns\InteractsWithActions;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TaskKanban extends Page implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'task::filament.pages.task-kanban';

    public array $statuses = [];

    public EloquentCollection|Collection $tasks;

    public Task $currentTask;

    public function mount(): void
    {
        $this->statuses = TaskStatus::cases();

        $this->tasks = collect($this->statuses)
            ->mapWithKeys(fn ($status) => [$status->value => collect()]);

        Task::all()->groupBy('status')->each(fn ($tasks, $status) => $this->tasks[$status] = $tasks);
    }

    public function movedTask(string $taskId, string $fromStatusString, string $toStatusString): JsonResponse
    {
        $fromStatus = TaskStatus::from($fromStatusString);
        $toStatus = TaskStatus::from($toStatusString);

        $task = $this->tasks[$fromStatusString]->firstWhere('id', $taskId);

        try {
            $task->getStateMachine('status')->transitionTo($toStatus);
        } catch (InvalidTransition $e) {
            return response()->json([
                'success' => false,
                'message' => "Cannot transition from \"{$fromStatus->displayName()}\" to \"{$toStatus->displayName()}\".",
            ], ResponseAlias::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Task could not be moved. Something went wrong, if this continues please contact support.',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }

        $this->tasks[$fromStatusString] = $this->tasks[$fromStatusString]->filter(fn ($task) => $task->id !== $taskId);
        $this->tasks[$toStatusString]->push($task);

        return response()->json([
            'success' => true,
            'message' => 'Task moved successfully.',
        ], ResponseAlias::HTTP_OK);
    }

    public function editTask(Task $task)
    {
        $this->currentTask = $task;

        $this->mountAction('edit');
    }

    public function editAction()
    {
        return EditAction::make('edit')
            ->record($this->currentTask)
            ->form([
                TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->string(),
                DateTimePicker::make('due')
                    ->label('Due Date')
                    ->native(false),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->nullable()
                    ->searchable(['name', 'email'])
                    ->default(auth()->id()),
                MorphToSelect::make('concern')
                    ->label('Concern')
                    ->searchable()
                    ->preload()
                    ->types([
                        Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
            ])
            ->using(function (Model $record, array $data): Model {
                $data = collect($data);

                /** @var Task $record */
                $record->fill($data->except('assigned_to')->toArray());

                $record->assigned_to = $data->get('assigned_to');

                $record->save();

                return $record;
            });
    }
}
