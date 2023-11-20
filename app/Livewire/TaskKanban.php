<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Assist\Task\Models\Task;
use Filament\Actions\EditAction;
use Assist\Task\Enums\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Assist\Task\Filament\Concerns\TaskEditForm;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use Assist\Task\Filament\Pages\Components\TaskKanbanViewAction;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Assist\Task\Filament\Resources\TaskResource\Pages\ListTasks;

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
                'message' => "Cannot transition from \"{$fromStatus->displayName()}\" to \"{$toStatus->displayName()}\".",
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
