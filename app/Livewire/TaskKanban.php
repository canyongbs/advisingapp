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

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use AdvisingApp\Task\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use AdvisingApp\Task\Enums\TaskStatus;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Forms\Components\EducatableSelect;
use Filament\Actions\Concerns\InteractsWithActions;
use AdvisingApp\Task\Filament\Concerns\TaskEditForm;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use AdvisingApp\Task\Filament\Pages\Components\TaskKanbanViewAction;
use AdvisingApp\Task\Filament\Resources\TaskResource\Pages\ListTasks;

class TaskKanban extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use TaskEditForm;
    use InteractsWithPageTable;

    public array $statuses = [];

    public ?Task $currentTask = null;

    public function render()
    {
        return view('livewire.task-kanban', [
            'tasks' => $this->getTasks(),
        ]);
    }

    public function mount(): void
    {
        $this->statuses = TaskStatus::cases();
    }

    public function getTasks(): Collection
    {
        $pageTasks = $this->getPageTableQuery()->get()->groupBy('status');

        return collect($this->statuses)
            ->mapWithKeys(fn ($status) => [$status->value => $pageTasks[$status->value] ?? collect()]);
    }

    public function movedTask(string $taskId, string $fromStatusString, string $toStatusString): JsonResponse
    {
        $fromStatus = TaskStatus::from($fromStatusString);
        $toStatus = TaskStatus::from($toStatusString);

        $task = $this->getPageTableQuery()->firstWhere('id', $taskId);

        try {
            $task->getStateMachine('status')->transitionTo($toStatus);
        } catch (InvalidTransition $e) {
            return response()->json([
                'success' => false,
                'message' => "Cannot transition from \"{$fromStatus->getLabel()}\" to \"{$toStatus->getLabel()}\".",
            ], ResponseAlias::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Task could not be moved. Something went wrong, if this continues please contact support.',
            ], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task moved successfully.',
        ], ResponseAlias::HTTP_OK);
    }

    public function viewTask(Task $task)
    {
        $this->currentTask = $task;

        $this->mountAction('view');
    }

    public function createTaskAction(): Action
    {
        return Action::make('createTask')
            ->model(Task::class)
            ->form([
                TextInput::make('title')
                    ->required()
                    ->maxLength(100)
                    ->string(),
                Textarea::make('description')
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
                EducatableSelect::make('concern')
                    ->label('Related To'),
            ])
            ->action(function (array $data, array $arguments) {
                $record = new Task(Arr::except($data, 'assigned_to'));
                $record->assigned_to = $data['assigned_to'] ?? null;
                $record->status = $arguments['status'] ?? null;
                $record->save();

                Notification::make()
                    ->success()
                    ->title('Created task')
                    ->send();
            });
    }

    public function viewAction()
    {
        return TaskKanbanViewAction::make()->record($this->currentTask)
            ->extraModalFooterActions(
                [
                    EditAction::make('edit')
                        ->record($this->currentTask)
                        ->form($this->editFormFields()),
                ]
            );
    }

    protected function getTablePage(): string
    {
        return ListTasks::class;
    }
}
