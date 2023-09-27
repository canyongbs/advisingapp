<?php

namespace Assist\Engagement\Filament\Resources\EngagementResponseResource\Components;

use Filament\Actions\ViewAction;
use Assist\Engagement\Filament\Concerns\EngagementResponseInfolist;

// TODO Move this
class EngagementResponseViewAction extends ViewAction
{
    use EngagementResponseInfolist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->infolist($this->engagementResponseInfolist());
    }
}
