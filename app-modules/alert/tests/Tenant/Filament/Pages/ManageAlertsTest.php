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

namespace AdvisingApp\Alert\Tests\Tenant\Filament\Pages;

use AdvisingApp\Alert\Filament\Pages\ManageAlerts;
use AdvisingApp\Alert\Jobs\RemoveAlertFiltersFromGroupsJob;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

beforeEach(function () {
    DB::statement('DROP VIEW IF EXISTS student_alerts');
});

it('saves without showing modal when no alerts are being disabled', function () {
    asSuperAdmin();

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->disabled()
        ->create();

    $formData = [
        (string) $alertConfig->id => [
            'is_enabled' => true,
        ],
    ];

    Livewire::test(ManageAlerts::class)
        ->set('data', $formData)
        ->call('save')
        ->assertHasNoErrors();

    $alertConfig->refresh();
    expect($alertConfig->is_enabled)->toBeTrue();
});

it('saves without showing modal when disabling alerts not used by any groups', function () {
    asSuperAdmin();

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $formData = [
        (string) $alertConfig->id => [
            'is_enabled' => false,
        ],
    ];

    Livewire::test(ManageAlerts::class)
        ->set('data', $formData)
        ->call('save')
        ->assertHasNoErrors();

    $alertConfig->refresh();
    expect($alertConfig->is_enabled)->toBeFalse();
});

it('shows confirmation modal when disabling alerts used by groups', function () {
    asSuperAdmin();

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    Group::factory()
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
                                    'alert_configuration_id' => (string) $alertConfig->id,
                                    'status' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $formData = [
        (string) $alertConfig->id => [
            'is_enabled' => false,
        ],
    ];

    Livewire::test(ManageAlerts::class)
        ->set('data', $formData)
        ->call('save')
        ->assertActionMounted('confirmDisableAlerts');
});

it('dispatches job when proceeding with disabling alerts used by groups', function () {
    Queue::fake([RemoveAlertFiltersFromGroupsJob::class]);

    asSuperAdmin();

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    Group::factory()
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
                                    'alert_configuration_id' => (string) $alertConfig->id,
                                    'status' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $formData = [
        (string) $alertConfig->id => [
            'is_enabled' => false,
        ],
    ];

    Livewire::test(ManageAlerts::class)
        ->set('data', $formData)
        ->call('save')
        ->callMountedAction();

    Queue::assertPushed(RemoveAlertFiltersFromGroupsJob::class, function (RemoveAlertFiltersFromGroupsJob $job) use ($alertConfig) {
        return in_array((string) $alertConfig->id, $job->alertConfigurationIds, true);
    });

    $alertConfig->refresh();
    expect($alertConfig->is_enabled)->toBeFalse();
});

it('does not dispatch job when save proceeds without modal', function () {
    Queue::fake([RemoveAlertFiltersFromGroupsJob::class]);

    asSuperAdmin();

    $alertConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $formData = [
        (string) $alertConfig->id => [
            'is_enabled' => false,
        ],
    ];

    Livewire::test(ManageAlerts::class)
        ->set('data', $formData)
        ->call('save')
        ->assertHasNoErrors();

    Queue::assertNotPushed(RemoveAlertFiltersFromGroupsJob::class);
});

it('detects multiple alerts being disabled that are used by different groups', function () {
    asSuperAdmin();

    $dorfConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::DorfGrade])
        ->enabled()
        ->create();

    $firstGenConfig = AlertConfiguration::factory()
        ->state(['preset' => AlertPreset::FirstGenerationStudent])
        ->enabled()
        ->create();

    Group::factory()
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
                                    'alert_configuration_id' => (string) $dorfConfig->id,
                                    'status' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    Group::factory()
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
                                    'alert_configuration_id' => (string) $firstGenConfig->id,
                                    'status' => '1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ])
        ->create();

    $formData = [
        (string) $dorfConfig->id => [
            'is_enabled' => false,
        ],
        (string) $firstGenConfig->id => [
            'is_enabled' => false,
        ],
    ];

    Livewire::test(ManageAlerts::class)
        ->set('data', $formData)
        ->call('save')
        ->assertActionMounted('confirmDisableAlerts');
});
