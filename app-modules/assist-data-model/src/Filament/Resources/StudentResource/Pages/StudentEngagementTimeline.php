<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Filament\Pages\TimelinePage;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class StudentEngagementTimeline extends TimelinePage
{
    protected static string $resource = StudentResource::class;

    protected static ?string $navigationLabel = 'Timeline';

    public string $emptyStateMessage = 'There are no engagements to show for this student.';

    public string $noMoreRecordsMessage = "You have reached the end of this student's engagement timeline.";

    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];
}
