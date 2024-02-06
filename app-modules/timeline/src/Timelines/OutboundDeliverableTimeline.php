<?php

namespace AdvisingApp\Timeline\Timelines;

use Filament\Actions\ViewAction;
use AdvisingApp\Timeline\Models\CustomTimeline;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\Notification\Filament\Actions\OutboundDeliverableViewAction;

class OutboundDeliverableTimeline extends CustomTimeline
{
    public function __construct(
        public OutboundDeliverable $outboundDeliverable
    ) {}

    public function icon(): string
    {
        return match ($this->outboundDeliverable->related::class) {
            ServiceRequest::class => match ($this->outboundDeliverable->channel) {
                NotificationChannel::Email => 'heroicon-o-envelope',
                NotificationChannel::Sms => 'heroicon-o-chat',
                NotificationChannel::Database => 'heroicon-o-circle-stack',
            },
            default => 'heroicon-o-arrow-up-tray',
        };
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
