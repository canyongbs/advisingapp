<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Concern\Enums\SystemConcernStatusClassification;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Filament\Widgets\ProspectStats;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use App\Settings\LicenseSettings;

it('returns correct stats for prospects within the given date range', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->caseManagement = true;
    $settings->save();

    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);
    $count = random_int(1, 5);

    EngagementResponse::factory()->count($count)->state([
        'sender_type' => (new Prospect())->getMorphClass(),
        'status' => EngagementResponseStatus::New,
        'created_at' => $startDate,
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => Prospect::factory(),
        'status_id' => ConcernStatus::factory()->create([
            'classification' => SystemConcernStatusClassification::Active,
        ])->getKey(),
        'created_at' => $startDate,
    ])->create();

    Task::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => Prospect::factory(),
        'status' => TaskStatus::Pending,
        'is_confidential' => false,
        'created_at' => $startDate,
    ])->create();

    EngagementResponse::factory()->count($count)->state([
        'sender_type' => (new Prospect())->getMorphClass(),
        'status' => EngagementResponseStatus::Actioned,
        'created_at' => $endDate,
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => Prospect::factory(),
        'status_id' => ConcernStatus::factory()->create([
            'classification' => SystemConcernStatusClassification::Resolved,
        ])->getKey(),
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => Prospect::factory(),
        'status' => TaskStatus::Completed,
        'is_confidential' => false,
        'created_at' => $endDate,
    ])->create();

    EngagementResponse::factory()->count($count)->state([
        'sender_type' => (new Prospect())->getMorphClass(),
        'status' => EngagementResponseStatus::New,
        'created_at' => now()->subDays(20),
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => Prospect::factory(),
        'status_id' => ConcernStatus::factory()->create([
            'classification' => SystemConcernStatusClassification::Active,
        ])->getKey(),
        'created_at' => now()->subDays(20),
    ])->create();

    $widget = new ProspectStats();
    $widget->activeTab = ActionCenterTab::All->value;
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(8)
        ->and($stats[0]->getLabel())->toEqual('New Messages')
        ->and($stats[0]->getValue())->toEqual($count)
        ->and($stats[1]->getLabel())->toEqual('Open Concerns')
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getLabel())->toEqual('Open Tasks')
        ->and($stats[2]->getValue())->toEqual($count)
        ->and($stats[3]->getLabel())->toEqual('Actioned Messages')
        ->and($stats[3]->getValue())->toEqual($count)
        ->and($stats[4]->getLabel())->toEqual('Closed Concerns')
        ->and($stats[4]->getValue())->toEqual($count)
        ->and($stats[5]->getLabel())->toEqual('Closed Tasks')
        ->and($stats[5]->getValue())->toEqual($count);
});

it('returns correct stats for prospects based on group filter', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->caseManagement = true;
    $settings->save();

    $count = random_int(1, 5);

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

    $johnProspect = Prospect::factory()->create(['last_name' => 'John']);
    $doeProspect = Prospect::factory()->create(['last_name' => 'Doe']);

    $openConcernStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Active,
    ]);
    $closedConcernStatus = ConcernStatus::factory()->create([
        'classification' => SystemConcernStatusClassification::Resolved,
    ]);

    EngagementResponse::factory()->count($count)->state([
        'sender_type' => (new Prospect())->getMorphClass(),
        'sender_id' => $johnProspect->getKey(),
        'status' => EngagementResponseStatus::New,
    ])->create();

    EngagementResponse::factory()->count($count)->state([
        'sender_type' => (new Prospect())->getMorphClass(),
        'sender_id' => $johnProspect->getKey(),
        'status' => EngagementResponseStatus::Actioned,
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => $johnProspect->getKey(),
        'status_id' => $openConcernStatus->getKey(),
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => $johnProspect->getKey(),
        'status_id' => $closedConcernStatus->getKey(),
    ])->create();

    Task::factory()->count($count)->concerningProspect($johnProspect)->state([
        'status' => TaskStatus::Pending,
        'is_confidential' => false,
    ])->create();

    Task::factory()->count($count)->concerningProspect($johnProspect)->state([
        'status' => TaskStatus::Completed,
        'is_confidential' => false,
    ])->create();

    EngagementResponse::factory()->count($count)->state([
        'sender_type' => (new Prospect())->getMorphClass(),
        'sender_id' => $doeProspect->getKey(),
        'status' => EngagementResponseStatus::New,
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_type' => (new Prospect())->getMorphClass(),
        'concern_id' => $doeProspect->getKey(),
        'status_id' => $openConcernStatus->getKey(),
    ])->create();

    Task::factory()->count($count)->concerningProspect($doeProspect)->state([
        'status' => TaskStatus::Pending,
        'is_confidential' => false,
    ])->create();

    $widget = new ProspectStats();
    $widget->activeTab = ActionCenterTab::All->value;
    $widget->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(8)
        ->and($stats[0]->getLabel())->toEqual('New Messages')
        ->and($stats[0]->getValue())->toEqual($count)
        ->and($stats[1]->getLabel())->toEqual('Open Concerns')
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getLabel())->toEqual('Open Tasks')
        ->and($stats[2]->getValue())->toEqual($count)
        ->and($stats[3]->getLabel())->toEqual('Actioned Messages')
        ->and($stats[3]->getValue())->toEqual($count)
        ->and($stats[4]->getLabel())->toEqual('Closed Concerns')
        ->and($stats[4]->getValue())->toEqual($count)
        ->and($stats[5]->getLabel())->toEqual('Closed Tasks')
        ->and($stats[5]->getValue())->toEqual($count);
});

it('only shows case stats when the case management feature is active', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->caseManagement = false;
    $settings->save();

    $widget = new ProspectStats();
    $widget->activeTab = ActionCenterTab::All->value;

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(6)
        ->and($stats[0]->getLabel())->toEqual('New Messages')
        ->and($stats[1]->getLabel())->toEqual('Open Concerns')
        ->and($stats[2]->getLabel())->toEqual('Open Tasks')
        ->and($stats[3]->getLabel())->toEqual('Actioned Messages')
        ->and($stats[4]->getLabel())->toEqual('Closed Concerns')
        ->and($stats[5]->getLabel())->toEqual('Closed Tasks');

    $settings->data->addons->caseManagement = true;
    $settings->save();

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(8)
        ->and($stats[0]->getLabel())->toEqual('New Messages')
        ->and($stats[1]->getLabel())->toEqual('Open Concerns')
        ->and($stats[2]->getLabel())->toEqual('Open Tasks')
        ->and($stats[3]->getLabel())->toEqual('Open Cases')
        ->and($stats[4]->getLabel())->toEqual('Actioned Messages')
        ->and($stats[5]->getLabel())->toEqual('Closed Concerns')
        ->and($stats[6]->getLabel())->toEqual('Closed Tasks')
        ->and($stats[7]->getLabel())->toEqual('Closed Cases');
});
