<?php

use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Filament\Pages\Timeline;
use Assist\Engagement\Models\EngagementResponse;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class StudentEngagementTimeline extends Timeline
{
    protected static string $resource = StudentResource::class;

    public string $emptyStateMessage = 'There are no engagements to show for this student.';

    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public function mount($record): void
    {
        $this->recordModel = $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->aggregateRecords();
    }
}
