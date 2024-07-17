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
use AdvisingApp\Ai\Models\PromptUse;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Ai\Models\PromptUpvote;
use AdvisingApp\Authorization\Models\License;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\Authorization\Enums\LicenseType;

class AiStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('AI Users', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('ai-users-count', now()->addHours(24), function (): int {
                    return License::where('type', LicenseType::ConversationalAi)->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompts Liked', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('prompts-liked-count', now()->addHours(24), function (): int {
                    return PromptUpvote::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompt Insertions', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('prompts-insertions-count', now()->addHours(24), function (): int {
                    return PromptUse::count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
