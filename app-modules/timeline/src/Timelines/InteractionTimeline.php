<?php

namespace AdvisingApp\Timeline\Timelines;

use AdvisingApp\Interaction\Filament\Resources\InteractionResource\Components\InteractionViewAction;
use AdvisingApp\Interaction\Models\Interaction;
use Filament\Actions\ViewAction;
use AdvisingApp\Timeline\Models\CustomTimeline;

class InteractionTimeline extends CustomTimeline
{
    public function __construct(
        public Interaction $interaction
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-arrow-small-right';
    }

    public function sortableBy(): string
    {
        return $this->interaction->created_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'interaction::interaction-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return InteractionViewAction::make()->record($this->interaction);
    }
}
