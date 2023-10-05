<?php

namespace Assist\Engagement\Filament\Concerns;

use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Assist\Engagement\Enums\EngagementDeliveryStatus;

// TODO Re-use this trait across other places where infolist is rendered
trait EngagementInfolist
{
    public function engagementInfolist(): array
    {
        return [
            TextEntry::make('user.name')
                ->label('Created By'),
            Fieldset::make('Content')
                ->schema([
                    TextEntry::make('subject'),
                    TextEntry::make('body'),
                ]),
            RepeatableEntry::make('deliverables')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('channel'),
                    IconEntry::make('delivery_status')
                        ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                            EngagementDeliveryStatus::Successful => 'heroicon-o-check-circle',
                            EngagementDeliveryStatus::Awaiting => 'heroicon-o-clock',
                            EngagementDeliveryStatus::Failed => 'heroicon-o-x-circle',
                        })
                        ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                            EngagementDeliveryStatus::Successful => 'success',
                            EngagementDeliveryStatus::Awaiting => 'info',
                            EngagementDeliveryStatus::Failed => 'danger',
                        }),
                    TextEntry::make('delivered_at'),
                    TextEntry::make('delivery_response'),
                ])
                ->columns(2),
        ];
    }
}
