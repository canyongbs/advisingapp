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

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectEngagementLineChart;

it('returns correct monthly email and sms engagement data for prospects within the given date range', function () {
    $startDate = now()->subMonths(3);
    $endDate = now()->subDays(5);

    $prospect1 = Prospect::factory()->state(['created_at' => $startDate])->create();
    $prospect2 = Prospect::factory()->state(['created_at' => $endDate])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospect1->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospect2->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widgetInstance = new ProspectEngagementLineChart();
    $widgetInstance->cacheTag = 'report-prospect-engagement';
    $widgetInstance->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData()['datasets'][0]['data'])->toMatchSnapshot();
});

it('returns correct monthly email and sms engagement data for prospects based on group filters', function () {
    $startDate = now()->subMonths(3);
    $endDate = now()->subDays(5);

    $group = Group::factory()->create([
        'model' => GroupModel::Prospect,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last_name',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'John',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $prospectOne = Prospect::factory()->state(['created_at' => $startDate, 'last_name' => 'John'])->create();
    $prospectTwo = Prospect::factory()->state(['created_at' => $endDate, 'last_name' => 'John'])->create();
    $prospectThree = Prospect::factory()->state(['created_at' => $startDate, 'last_name' => 'Doe'])->create();
    $prospectFour = Prospect::factory()->state(['created_at' => $endDate, 'last_name' => 'Doe'])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospectOne->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospectTwo->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospectThree->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospectFour->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widgetInstance = new ProspectEngagementLineChart();
    $widgetInstance->cacheTag = 'report-prospect-engagement';
    $widgetInstance->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $dataWithGroup = $widgetInstance->getData();

    expect($dataWithGroup)
        ->not->toBeEmpty()
        ->and($dataWithGroup['datasets'][0]['data'])->toMatchSnapshot();

    $widgetInstance = new ProspectEngagementLineChart();
    $widgetInstance->cacheTag = 'report-prospect-engagement';
    $widgetInstance->pageFilters = [];

    $dataWithoutGroup = $widgetInstance->getData();

    expect($dataWithoutGroup)
        ->not->toBeEmpty()
        ->and($dataWithoutGroup['datasets'][0]['data'])->toMatchSnapshot();
});
