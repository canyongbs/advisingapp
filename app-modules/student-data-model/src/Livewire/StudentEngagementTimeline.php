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

namespace AdvisingApp\StudentDataModel\Livewire;

use App\Actions\GetRecordFromMorphAndKey;
use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Timeline\Filament\Pages\TimelinePage;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;

class StudentEngagementTimeline extends TimelinePage
{
    protected static string $resource = StudentResource::class;

    protected static ?string $navigationLabel = 'Timeline';

    protected static string $view = 'student-data-model::livewire.student-engagement-timeline';

    public string $emptyStateMessage = 'There are no engagements to show for this student.';

    public string $noMoreRecordsMessage = "You have reached the end of this student's engagement timeline.";

    public bool $isShowFullFeed = false;

    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
        AlertHistory::class,
        TaskHistory::class,
        Interaction::class,
    ];

    public function openFullFeedModal(): void
    {
        $this->dispatch('open-modal', id: 'show-full-feed');
    }

    public function closeFullFeedModal(): void
    {
        $this->dispatch('close-modal', id: 'show-full-feed');
    }

    public function fetchTitle($morphReference, $key): ?string
    {
        $record = resolve(GetRecordFromMorphAndKey::class)->via($morphReference, $key);

        return match ($morphReference) {
            'interaction', 'engagement' => $record?->user?->name,
            'engagement_response' => $record?->sender?->full_name,
            'task_history' => 'Task ' . $record?->timeline()?->history?->event,
            'alert_history' => 'Alert ' . $record?->timeline()?->history?->event,
        };
    }
}
