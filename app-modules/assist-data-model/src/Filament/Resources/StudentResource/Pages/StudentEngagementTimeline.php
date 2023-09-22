<?php

use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\EngagementResponse;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\Timeline;

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
