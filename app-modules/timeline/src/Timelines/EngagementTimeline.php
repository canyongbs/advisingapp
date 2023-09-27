<?php

namespace Assist\Timeline\Timelines;

use Filament\Actions\ViewAction;
use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Models\CustomTimeline;
use Assist\Engagement\Filament\Resources\EngagementResource\Components\EngagementViewAction;

// TODO Decide where these belong - might want to keep these in the context of the original module
class EngagementTimeline extends CustomTimeline
{
    public function __construct(
        public Engagement $engagement
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-arrow-small-right';
    }

    public function sortableBy(): string
    {
        return $this->engagement->deliver_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'engagement::engagement-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return EngagementViewAction::make()->record($this->engagement);
    }
}
