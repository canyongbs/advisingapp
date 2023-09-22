<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Filament\Pages\Timeline;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ProspectEngagementTimeline extends Timeline
{
    protected static string $resource = ProspectResource::class;

    public string $emptyStateMessage = 'There are no engagements to show for this prospect.';

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
