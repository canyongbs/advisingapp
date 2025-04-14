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

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Timeline\Livewire\Concerns\CanLoadTimelineRecords;
use AdvisingApp\Timeline\Livewire\Concerns\HasTimelineRecords;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

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

    #[Locked]
    public string $viewUrl;

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
            'interaction' => 'Interaction Created',
            'engagement' => match ($record->getDeliveryMethod()) {
                NotificationChannel::Sms => 'SMS Sent',
                default => 'Email Sent',
            },
            'engagement_response' => $record->sender?->full_name,
            'task_history' => 'Task ' . $record->event,
            'alert_history' => 'Alert ' . $record->event,
        };
    }

    public function getTimelineRecordDescription(Model $record): ?string
    {
        return (string) str(match ($record->getMorphClass()) {
            'interaction' => "Subject: {$record->subject}",
            'engagement' => match ($record->getDeliveryMethod()) {
                NotificationChannel::Sms => "Preview: {$record->getBodyMarkdown()}",
                default => "Subject: {$record->getSubjectMarkdown()}",
                // default => "Subject: {$record->subject}",
            },
            'engagement_response' => 'Preview: ' . str($record->getBody())->stripTags(),
            'task_history' => "Title: {$record->subject?->title}",
            'alert_history' => "{$record->subject?->severity->getLabel()} severity, " . str($record->subject?->description)->limit(200),
        })->limit(110);
    }

    public function getTimelineRecordUser(Model $record): ?User
    {
        return match ($record->getMorphClass()) {
            'interaction', 'engagement' => $record->user,
            'task_history', 'alert_history' => $record->subject?->createdBy,
            default => null,
        };
    }
    // @endtodo

    public static function canViewForRecord(Educatable&Model $educatable): bool
    {
        if ($educatable instanceof Prospect) {
            return auth()->user()->can([
                'prospect.view-any',
                'prospect.*.view',
                'engagement.view-any',
                'engagement.*.view',
            ]);
        }

        if ($educatable instanceof Student) {
            return auth()->user()->can([
                'student.view-any',
                'student.*.view',
                'engagement.view-any',
                'engagement.*.view',
            ]);
        }

        return false;
    }
}
