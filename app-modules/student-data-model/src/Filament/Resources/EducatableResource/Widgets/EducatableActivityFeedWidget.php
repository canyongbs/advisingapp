<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Contracts\HasActions;
use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Engagement\Models\Engagement;
use Filament\Infolists\Contracts\HasInfolists;
use AdvisingApp\Interaction\Models\Interaction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use AdvisingApp\Engagement\Models\EngagementResponse;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Timeline\Livewire\Concerns\HasTimelineRecords;
use AdvisingApp\Timeline\Livewire\Concerns\CanLoadTimelineRecords;

class EducatableActivityFeedWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use HasTimelineRecords; // @todo: Refactor
    use CanLoadTimelineRecords; // @todo: Refactor
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-activity-feed-widget';

    #[Locked]
    public Educatable&Model $educatable;

    public function mount(): void
    {
        // @todo: Refactor
        $this->recordModel = $this->educatable;
        $this->modelsToTimeline = [
            Engagement::class,
            EngagementResponse::class,
            AlertHistory::class,
            TaskHistory::class,
            Interaction::class,
        ];
        // @endtodo
    }

    // @todo: Refactor
    public function getTimelineRecordTitle(Model $record): ?string
    {
        return match ($record->getMorphClass()) {
            'interaction', 'engagement' => $record->user?->name,
            'engagement_response' => $record->sender?->full_name,
            'task_history' => 'Task ' . $record->event,
            'alert_history' => 'Alert ' . $record->event,
        };
    }

    public function getTimelineRecordDescription(Model $record): ?string
    {
        return (string) str(match ($record->getMorphClass()) {
            'interaction', 'engagement' => "Subject: {$record->subject}",
            'engagement_response' => 'Preview: {$record->content}',
            'task_history' => "Title: {$record->subject?->title}",
            'alert_history' => "{$record->subject?->severity->getLabel()} severity, " . str($record->subject?->description)->limit(200),
        })->limit(110);
    }

    public function getTimelineRecordUser(Model $record): ?User
    {
        return match ($record->getMorphClass()) {
            'interaction', 'engagement' => $record->user,
            'task_history', 'alert_history' => $record->subject->createdBy,
            default => null,
        };
    }
    // @endtodo

    public static function canView(): bool
    {
        return auth()->user()->can('timeline.access');
    }
}
