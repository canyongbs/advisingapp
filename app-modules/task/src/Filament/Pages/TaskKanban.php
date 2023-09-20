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
    public function movedTask()
    {
        ray('here');
    }
}
