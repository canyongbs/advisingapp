<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;
use AdvisingApp\Task\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

class EducatableTasksWidget extends Widget
{
    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-tasks-widget';

    #[Locked]
    public Educatable&Model $educatable;

    #[Locked]
    public string $manageUrl;

    public static function canView(): bool
    {
        return auth()->user()->can('task.view-any');
    }

    protected function getStatusCounts(): array
    {
        $counts = $this->educatable->tasks()
            ->toBase()
            ->selectRaw('count(*) as task_count, status')
            ->groupBy('status')
            ->pluck('task_count', 'status');

        return collect(TaskStatus::cases())
            ->reverse()
            ->mapWithKeys(fn (TaskStatus $taskStatus): array => [$taskStatus->getLabel() => $counts[$taskStatus->value] ?? 0])
            ->filter()
            ->all();
    }
}
