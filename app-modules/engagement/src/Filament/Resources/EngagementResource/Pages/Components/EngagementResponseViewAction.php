<?php

namespace Assist\Engagement\Filament\Pages\Components;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;

class EngagementResponseViewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->infolist([
                TextEntry::make('content')
                    ->translateLabel(),
                TextEntry::make('sent_at')
                    ->dateTime('Y-m-d H:i:s'),
            ]);
    }
}
