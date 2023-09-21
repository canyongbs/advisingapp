<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

abstract class EngagementTimeline extends Page
{
    use InteractsWithRecord;

    protected static string $view = 'engagement::filament.pages.engagement-timeline';

    public $aggregateEngagements;

    abstract public function aggregateEngagements(): Collection;

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        // TODO We also need to check access for the other entities that are going to be included in the timeline
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }
}
