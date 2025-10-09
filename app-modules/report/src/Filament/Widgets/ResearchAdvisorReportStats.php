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

use AdvisingApp\Research\Models\ResearchRequest;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class ResearchAdvisorReportStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $researchAdvisorsCount = $shouldBypassCache
            ? ResearchRequest::query()
                ->whereNotNull('title')
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'research-advisors-count',
                now()->addHours(24),
                fn (): int => ResearchRequest::query()
                    ->whereNotNull('title')
                    ->count(),
            );

        $uniqueUsersCount = $shouldBypassCache
            ? ResearchRequest::query()
                ->whereNotNull('title')
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->distinct('user_id')
                ->count('user_id')
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'research-advisor-unique-users-count',
                now()->addHours(24),
                fn (): int => ResearchRequest::query()
                    ->whereNotNull('title')
                    ->distinct('user_id')
                    ->count('user_id'),
            );

        $sourcesCount = $shouldBypassCache
            ? DB::selectOne(
                '
                WITH filtered_research_requests AS (
                    SELECT id, sources FROM research_requests
                    WHERE title IS NOT NULL
                    AND created_at BETWEEN ? AND ?
                )
                SELECT
                    (SELECT COUNT(*) FROM research_request_parsed_files WHERE research_request_id IN (SELECT id FROM filtered_research_requests))
                    +
                    (SELECT COUNT(*) FROM research_request_parsed_links WHERE research_request_id IN (SELECT id FROM filtered_research_requests))
                    +
                    (SELECT COUNT(*) FROM research_request_parsed_search_results WHERE research_request_id IN (SELECT id FROM filtered_research_requests))
                    +
                    (SELECT COALESCE(SUM(jsonb_array_length(sources)), 0) FROM filtered_research_requests)
                    AS total',
                [$startDate, $endDate]
            )->total
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'research-advisor-sources-count',
                now()->addHours(24),
                fn (): int => DB::selectOne('
                    WITH filtered_research_requests AS (
                        SELECT id, sources FROM research_requests
                        WHERE title IS NOT NULL
                    )
                    SELECT
                        (SELECT COUNT(*) FROM research_request_parsed_files WHERE research_request_id IN (SELECT id FROM filtered_research_requests))
                        +
                        (SELECT COUNT(*) FROM research_request_parsed_links WHERE research_request_id IN (SELECT id FROM filtered_research_requests))
                        +
                        (SELECT COUNT(*) FROM research_request_parsed_search_results WHERE research_request_id IN (SELECT id FROM filtered_research_requests))
                        +
                        (SELECT COALESCE(SUM(jsonb_array_length(sources)), 0) FROM filtered_research_requests)
                        AS total')->total,
            );

        return [
            Stat::make('Research Advisors', Number::abbreviate($researchAdvisorsCount)),
            Stat::make('Active Users', Number::abbreviate($uniqueUsersCount)),
            Stat::make('Sources Used', Number::abbreviate($sourcesCount)),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
