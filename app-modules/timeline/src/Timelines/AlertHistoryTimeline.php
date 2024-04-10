<?php

namespace AdvisingApp\Timeline\Timelines;

use Filament\Actions\ViewAction;
use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Timeline\Models\CustomTimeline;
use AdvisingApp\Alert\Filament\Actions\AlertHistoryCreatedViewAction;
use AdvisingApp\Alert\Filament\Actions\AlertHistoryUpdatedViewAction;

class AlertHistoryTimeline extends CustomTimeline
{
    public function __construct(
        public AlertHistory $history
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-bell-alert';
    }

    public function sortableBy(): string
    {
        return $this->history->created_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return match ($this->history->event) {
            'created' => 'alert::created-history-timeline-item',
            'updated' => 'alert::updated-history-timeline-item',
            'status_changed' => 'alert::status-changed-history-timeline-item',
        };
    }

    public function modalViewAction(): ViewAction
    {
        return (match ($this->history->event) {
            'created' => AlertHistoryCreatedViewAction::make()
                ->modalHeading('View Alert'),
            'updated', 'status_changed' => AlertHistoryUpdatedViewAction::make()
                ->modalHeading('View Changes'),
        })->record($this->history);
    }
}
