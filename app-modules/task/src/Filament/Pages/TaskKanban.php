<?php

namespace Assist\Task\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Collection;

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

    #[On('moved-task')]
    public function movedTask(string $taskId, string $fromStatusString, string $toStatusString)
    {
        ray($this->tasks);

        $fromStatus = TaskStatus::from($fromStatusString);
        $toStatus = TaskStatus::from($toStatusString);

        $task = $this->tasks[$fromStatusString]->firstWhere('id', $taskId);

        $task->getStateMachine('status')->transitionTo($toStatus);
    }
}
