<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Filament\Pages\Timeline;
use Assist\Engagement\Models\EngagementResponse;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class StudentEngagementTimeline extends Timeline
{
    protected static string $resource = StudentResource::class;

    protected static ?string $navigationLabel = 'Engagement Timeline';

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
