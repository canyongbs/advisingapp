<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Filament\Concerns;

use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
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
            Fieldset::make('deliverable')
                ->label('Delivery Information')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('deliverable.channel')
                        ->label('Channel'),
                    IconEntry::make('deliverable.delivery_status')
                        ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                            EngagementDeliveryStatus::Successful => 'heroicon-o-check-circle',
                            EngagementDeliveryStatus::Awaiting => 'heroicon-o-clock',
                            EngagementDeliveryStatus::Failed => 'heroicon-o-x-circle',
                        })
                        ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                            EngagementDeliveryStatus::Successful => 'success',
                            EngagementDeliveryStatus::Awaiting => 'warning',
                            EngagementDeliveryStatus::Failed => 'danger',
                        })
                        ->label('Status'),
                    TextEntry::make('deliverable.delivered_at')
                        ->label('Delivered At'),
                    TextEntry::make('deliverable.delivery_response')
                        ->label('Response'),
                ])
                ->columns(2),
        ];
    }
}
