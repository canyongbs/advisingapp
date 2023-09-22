<?php

namespace Assist\Engagement\Filament\Pages\Components;

use Filament\Actions\ViewAction;
use Assist\Engagement\Filament\Concerns\EngagementResponseInfolist;

class EngagementResponseViewAction extends ViewAction
{
    use EngagementResponseInfolist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->infolist($this->engagementResponseInfolist());
    }
}
