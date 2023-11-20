<?php

namespace Assist\Engagement\Filament\Concerns;

use Filament\Infolists\Components\TextEntry;

// TODO Re-use this trait across other places where infolist is rendered
trait EngagementResponseInfolist
{
    public function engagementResponseInfolist(): array
    {
        return [
            TextEntry::make('content')
                ->translateLabel(),
            TextEntry::make('sent_at')
                ->dateTime('Y-m-d H:i:s'),
        ];
    }
}
