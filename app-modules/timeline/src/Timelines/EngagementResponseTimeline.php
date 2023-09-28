<?php

namespace Assist\Timeline\Timelines;

use Filament\Actions\ViewAction;
use Assist\Timeline\Models\CustomTimeline;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Filament\Resources\EngagementResponseResource\Components\EngagementResponseViewAction;

// TODO Decide where these belong - might want to keep these in the context of the original module
class EngagementResponseTimeline extends CustomTimeline
{
    public function __construct(
        public EngagementResponse $engagementResponse
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-arrow-small-left';
    }

    public function sortableBy(): string
    {
        return $this->engagementResponse->sent_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'engagement::engagement-response-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return EngagementResponseViewAction::make()->record($this->engagementResponse);
    }
}
