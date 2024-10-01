<?php

namespace AdvisingApp\Timeline\Timelines;

use Filament\Actions\ViewAction;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Timeline\Models\CustomTimeline;
use AdvisingApp\Interaction\Filament\Resources\InteractionResource\Components\InteractionViewAction;

class InteractionTimeline extends CustomTimeline
{
    public function __construct(
        public Interaction $interaction
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-pencil-square';
    }

    public function sortableBy(): string
    {
        return $this->interaction->start_datetime;
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
