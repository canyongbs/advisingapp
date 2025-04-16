<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Timeline\Timelines;

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Timeline\Models\CustomTimeline;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;

// TODO Decide where these belong - might want to keep these in the context of the original module
class EngagementTimeline extends CustomTimeline
{
    public function __construct(
        public Engagement $engagement
    ) {}

    public function icon(): string
    {
        return match ($this->engagement->getDeliveryMethod()) {
            NotificationChannel::Email => 'heroicon-o-envelope',
            NotificationChannel::Sms => 'heroicon-o-chat-bubble-left',
            default => 'heroicon-o-arrow-small-right',
        };
    }

    public function sortableBy(): string
    {
        return $this->engagement->scheduled_at ?? $this->engagement->created_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'engagement::engagement-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return ViewAction::make()
            ->infolist([
                TextEntry::make('user.name')
                    ->label('Created By'),
                Fieldset::make('Content')
                    ->schema([
                        TextEntry::make('subject')
                            ->hidden(fn ($state): bool => blank($state))
                            ->getStateUsing(fn (Engagement $engagement): HtmlString => $engagement->getSubject())
                            ->columnSpanFull(),
                        TextEntry::make('body')
                            ->getStateUsing(fn (Engagement $engagement): HtmlString => $engagement->getBody())
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('delivery')
                    ->label('Delivery Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('channel')
                            ->label('Channel'),
                    ])
                    ->columns(2),
            ])
            ->record($this->engagement);
    }
}
