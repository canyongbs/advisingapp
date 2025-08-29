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

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectReportStats;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Task\Models\Task;

it('returns correct total prospect stats of prospects, alerts, segments and tasks within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospectCountStart = random_int(1, 5);
    $prospectCountEnd = random_int(1, 5);
    $alertCount = random_int(1, 5);
    $segmentCount = random_int(1, 5);
    $taskCount = random_int(1, 5);

    Prospect::factory()->count($prospectCountStart)->state([
        'created_at' => $startDate,
    ])->create();

    Prospect::factory()->count($prospectCountEnd)->state([
        'created_at' => $endDate,
    ])->create();

    Alert::factory()->count($alertCount)->state([
        'concern_id' => Prospect::factory(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    Segment::factory()->count($segmentCount)->state([
        'model' => SegmentModel::Prospect,
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => Prospect::factory(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $widget = new ProspectReportStats();
    $widget->cacheTag = 'prospect-report-cache';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($prospectCountStart + $prospectCountEnd)
        ->and($stats[1]->getValue())->toEqual($alertCount)
        ->and($stats[2]->getValue())->toEqual($segmentCount)
        ->and($stats[3]->getValue())->toEqual($taskCount);
});
