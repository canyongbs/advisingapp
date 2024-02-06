<?php

namespace AdvisingApp\Timeline\Timelines;

use AdvisingApp\Notification\Filament\Actions\OutboundDeliverableViewAction;
use AdvisingApp\Timeline\Models\CustomTimeline;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use Filament\Actions\Concerns\HasInfolist;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;

class OutboundDeliverableTimeline extends CustomTimeline
{
    public function __construct(
        public OutboundDeliverable $outboundDeliverable
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-adjustments-vertical';
    }

    public function sortableBy(): string
    {
        return $this->outboundDeliverable->created_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'notification::outbound-deliverable-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return OutboundDeliverableViewAction::make()
            ->record($this->outboundDeliverable)
            ->modalHeading('View Outbound Deliverable');
    }
}
