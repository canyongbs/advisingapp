<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Filament\Pages\TimelinePage;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ProspectEngagementTimeline extends TimelinePage
{
    protected static string $resource = ProspectResource::class;

    protected static ?string $navigationLabel = 'Engagement Timeline';

    public string $emptyStateMessage = 'There are no engagements to show for this prospect.';

    public string $noMoreRecordsMessage = "You have reached the end of this prospects's engagement timeline.";

    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];
}
