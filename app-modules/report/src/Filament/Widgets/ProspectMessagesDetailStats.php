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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class ProspectMessagesDetailStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $segmentId = $this->getSelectedSegment();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($segmentId);

        $emailsSentCount = $shouldBypassCache
            ? Engagement::query()
                ->whereHasMorph('recipient', Prospect::class, function (Builder $query) use ($segmentId) {
                    $query->when(
                        $segmentId,
                        fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                    );
                })
                ->where('channel', NotificationChannel::Email)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('dispatched_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'sent-emails-count',
                now()->addHours(24),
                fn () => Engagement::query()
                    ->whereHasMorph('recipient', Prospect::class)
                    ->where('channel', NotificationChannel::Email)
                    ->count()
            );

        $emailsReceivedCount = $shouldBypassCache
            ? EngagementResponse::query()
                ->whereHasMorph('sender', Prospect::class, function (Builder $query) use ($segmentId) {
                    $query->when(
                        $segmentId,
                        fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                    );
                })
                ->where('type', EngagementResponseType::Email)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('sent_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'received-emails-count',
                now()->addHours(24),
                fn () => EngagementResponse::query()
                    ->whereHasMorph('sender', Prospect::class)
                    ->where('type', EngagementResponseType::Email)
                    ->count()
            );

        $smsSentCount = $shouldBypassCache
            ? Engagement::query()
                ->whereHasMorph('recipient', Prospect::class, function (Builder $query) use ($segmentId) {
                    $query->when(
                        $segmentId,
                        fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                    );
                })
                ->where('channel', NotificationChannel::Sms)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('dispatched_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'sent-sms-count',
                now()->addHours(24),
                fn () => Engagement::query()
                    ->whereHasMorph('recipient', Prospect::class)
                    ->where('channel', NotificationChannel::Sms)
                    ->count()
            );

        $smsReceivedCount = $shouldBypassCache
            ? EngagementResponse::query()
                ->whereHasMorph('sender', Prospect::class, function (Builder $query) use ($segmentId) {
                    $query->when(
                        $segmentId,
                        fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                    );
                })
                ->where('type', EngagementResponseType::Sms)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('sent_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'received-sms-count',
                now()->addHours(24),
                fn () => EngagementResponse::query()
                    ->whereHasMorph('sender', Prospect::class)
                    ->where('type', EngagementResponseType::Sms)
                    ->count()
            );

        return [
            Stat::make('Emails Sent', Number::abbreviate($emailsSentCount, maxPrecision: 2)),
            Stat::make('Emails Received', Number::abbreviate($emailsReceivedCount, maxPrecision: 2)),
            Stat::make('SMS Sent', Number::abbreviate($smsSentCount, maxPrecision: 2)),
            Stat::make('SMS Received', Number::abbreviate($smsReceivedCount, maxPrecision: 2)),
        ];
    }
}
