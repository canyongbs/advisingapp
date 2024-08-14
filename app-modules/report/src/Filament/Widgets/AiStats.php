<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Support\Number;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Report\Models\TrackedEvent;
use AdvisingApp\Report\Enums\TrackedEventType;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Models\TrackedEventCount;

class AiStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Licensed Users', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('licensed-ai-users-count', now()->addHours(24), function (): int {
                    return LicenseType::ConversationalAi->getSeatsInUse();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Available Licenses', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('available-ai-licenses', now()->addHours(24), function (): int {
                    return LicenseType::ConversationalAi->getAvailableSeats();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Number of Assistants', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('ai-assistants-count', now()->addHours(24), function (): int {
                    return AiAssistant::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Exchanges', Cache::tags([$this->cacheTag])->remember('ai-exchanges', now()->addHours(24), function (): int|string {
                $count = TrackedEventCount::where('type', TrackedEventType::AiExchange)->first()?->count;

                return ! is_null($count) ? Number::abbreviate($count, maxPrecision: 2) : 'N/A';
            })),
            Stat::make('New Exchanges', Cache::tags([$this->cacheTag])->remember('ai-new-exchanges', now()->addHours(24), function (): int|string {
                return Number::abbreviate(
                    TrackedEvent::query()
                        ->where('type', TrackedEventType::AiExchange)
                        ->whereDate('occurred_at', '>=', now()->subDays(30))
                        ->count(),
                    maxPrecision: 2
                );
            })),
            Stat::make('Saved Conversations', Cache::tags([$this->cacheTag])->remember('ai-saved-conversations', now()->addHours(24), function (): int|string {
                $count = TrackedEventCount::where('type', TrackedEventType::AiThreadSaved)->first()?->count;

                return ! is_null($count) ? Number::abbreviate($count, maxPrecision: 2) : 'N/A';
            })),
        ];
    }
}
