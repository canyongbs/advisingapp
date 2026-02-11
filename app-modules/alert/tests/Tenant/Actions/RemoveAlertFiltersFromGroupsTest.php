<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Alert\Tests\Tenant\Actions;

use AdvisingApp\Alert\Actions\RemoveAlertFiltersFromGroups;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use Illuminate\Support\Str;

it('removes alert rules matching the given configuration ids', function () {
    $alertConfigId = (string) Str::uuid();

    $group = Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'r1' => [
                            'type' => 'alertStatus',
                            'data' => [
                                'operator' => 'alertStatus',
                                'isInverse' => false,
                                'settings' => [
                                    'alert_configuration_id' => $alertConfigId,
                                    'status' => '1',
                                ],
                            ],
                        ],
                        'r2' => [
                            'type' => 'last',
                            'data' => [
                                'operator' => 'contains',
                                'settings' => ['text' => 'Smith'],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([$alertConfigId]);

    expect($modifiedCount)->toBe(1);

    $group->refresh();
    $rules = $group->filters['queryBuilder']['rules'];

    expect($rules)->toHaveCount(1);
    expect(array_values($rules)[0]['type'])->toBe('last');
});

it('does not modify groups without matching alert rules', function () {
    $alertConfigId = (string) Str::uuid();
    $otherAlertConfigId = (string) Str::uuid();

    $group = Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'r1' => [
                            'type' => 'alertStatus',
                            'data' => [
                                'operator' => 'alertStatus',
                                'isInverse' => false,
                                'settings' => [
                                    'alert_configuration_id' => $otherAlertConfigId,
                                    'status' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([$alertConfigId]);

    expect($modifiedCount)->toBe(0);

    $group->refresh();
    $rules = $group->filters['queryBuilder']['rules'];

    expect($rules)->toHaveCount(1);
    expect(array_values($rules)[0]['type'])->toBe('alertStatus');
});

it('removes alert rules nested inside OR groups', function () {
    $alertConfigId = (string) Str::uuid();

    $group = Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'r1' => [
                            'type' => 'or',
                            'data' => [
                                'groups' => [
                                    [
                                        'rules' => [
                                            [
                                                'type' => 'alertStatus',
                                                'data' => [
                                                    'operator' => 'alertStatus',
                                                    'isInverse' => false,
                                                    'settings' => [
                                                        'alert_configuration_id' => $alertConfigId,
                                                        'status' => '1',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'rules' => [
                                            [
                                                'type' => 'last',
                                                'data' => [
                                                    'operator' => 'contains',
                                                    'settings' => ['text' => 'Smith'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([$alertConfigId]);

    expect($modifiedCount)->toBe(1);

    $group->refresh();
    $rules = $group->filters['queryBuilder']['rules'];

    $orRule = array_values($rules)[0];
    expect($orRule['type'])->toBe('or');
    expect($orRule['data']['groups'])->toHaveCount(1);
    expect($orRule['data']['groups'][0]['rules'][0]['type'])->toBe('last');
});

it('removes the entire OR rule when all nested groups become empty', function () {
    $alertConfigId = (string) Str::uuid();

    $group = Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'r1' => [
                            'type' => 'or',
                            'data' => [
                                'groups' => [
                                    [
                                        'rules' => [
                                            [
                                                'type' => 'alertStatus',
                                                'data' => [
                                                    'operator' => 'alertStatus',
                                                    'isInverse' => false,
                                                    'settings' => [
                                                        'alert_configuration_id' => $alertConfigId,
                                                        'status' => '1',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'r2' => [
                            'type' => 'last',
                            'data' => [
                                'operator' => 'contains',
                                'settings' => ['text' => 'Smith'],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([$alertConfigId]);

    expect($modifiedCount)->toBe(1);

    $group->refresh();
    $rules = $group->filters['queryBuilder']['rules'];

    expect($rules)->toHaveCount(1);
    expect(array_values($rules)[0]['type'])->toBe('last');
});

it('skips groups that are not student dynamic groups', function () {
    $alertConfigId = (string) Str::uuid();

    $prospectGroup = Group::factory()
        ->state([
            'model' => GroupModel::Prospect,
            'type' => GroupType::Dynamic,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'r1' => [
                            'type' => 'alertStatus',
                            'data' => [
                                'operator' => 'alertStatus',
                                'isInverse' => false,
                                'settings' => [
                                    'alert_configuration_id' => $alertConfigId,
                                    'status' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([$alertConfigId]);

    expect($modifiedCount)->toBe(0);

    $prospectGroup->refresh();
    expect($prospectGroup->filters['queryBuilder']['rules'])->toHaveCount(1);
});

it('skips groups with empty or null filters', function () {
    Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => null,
        ])
        ->create();

    Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => [],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([(string) Str::uuid()]);

    expect($modifiedCount)->toBe(0);
});

it('can remove multiple alert configuration ids at once', function () {
    $alertConfigId1 = (string) Str::uuid();
    $alertConfigId2 = (string) Str::uuid();

    $group = Group::factory()
        ->state([
            'model' => GroupModel::Student,
            'type' => GroupType::Dynamic,
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'r1' => [
                            'type' => 'alertStatus',
                            'data' => [
                                'operator' => 'alertStatus',
                                'isInverse' => false,
                                'settings' => [
                                    'alert_configuration_id' => $alertConfigId1,
                                    'status' => '1',
                                ],
                            ],
                        ],
                        'r2' => [
                            'type' => 'alertStatus',
                            'data' => [
                                'operator' => 'alertStatus',
                                'isInverse' => false,
                                'settings' => [
                                    'alert_configuration_id' => $alertConfigId2,
                                    'status' => '0',
                                ],
                            ],
                        ],
                        'r3' => [
                            'type' => 'last',
                            'data' => [
                                'operator' => 'contains',
                                'settings' => ['text' => 'Smith'],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $modifiedCount = app(RemoveAlertFiltersFromGroups::class)->execute([$alertConfigId1, $alertConfigId2]);

    expect($modifiedCount)->toBe(1);

    $group->refresh();
    $rules = $group->filters['queryBuilder']['rules'];

    expect($rules)->toHaveCount(1);
    expect(array_values($rules)[0]['type'])->toBe('last');
});
