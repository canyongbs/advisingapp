<?php

namespace Assist\Task\Filament\Pages;

use Exception;
use Filament\Pages\Page;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Collection;
use Bvtterfly\ModelStateMachine\Exceptions\InvalidTransition;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TaskKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'task::filament.pages.task-kanban';

    public array $statuses = [];

    public Collection $tasks;

    public function mount(): void
    {
        $this->statuses = TaskStatus::cases();

        $this->tasks = Task::all()->groupBy('status');
    }

    public function movedTask(string $taskId, string $fromStatusString, string $toStatusString)
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
}
