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

use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Report\Filament\Widgets\QnaAdvisorReportLineChart;
use Illuminate\Support\Carbon;

use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;

beforeEach()->skip('Skipping these tests as there are currently issues with these tests or the underlying functionality having to do with overflow dates that needs to be resolved');

it('returns correct QnaAdvisorMessage counts grouped by month within the given date range', function () {
    // Freeze now to a fixed date so the snapshot is deterministic and not flaky.
    $fixedNow = Carbon::parse('2025-10-15 12:00:00');
    travelTo($fixedNow);

    try {
        $startDate = now()->subMonths(3);
        $endDate = now()->subDays(5);

        QnaAdvisorMessage::factory()->count(5)->state([
            'created_at' => $startDate,
            'is_advisor' => false,
        ])->create();

        QnaAdvisorMessage::factory()->count(5)->state([
            'created_at' => $endDate,
            'is_advisor' => false,
        ])->create();

        $widgetInstance = new QnaAdvisorReportLineChart();
        $widgetInstance->cacheTag = 'qna-advisor-report-cache';
        $widgetInstance->pageFilters = [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ];

        expect($widgetInstance->getData()['datasets'][0]['data'])->toMatchSnapshot();
    } finally {
        travelBack();
    }
});
