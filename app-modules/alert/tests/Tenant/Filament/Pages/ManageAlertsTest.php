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

namespace AdvisingApp\Alert\Tests\Tenant\Filament\Pages;

use AdvisingApp\Alert\Enums\EarlyAlertTab;
use AdvisingApp\Alert\Filament\Pages\ManageAlerts;
use AdvisingApp\Alert\Jobs\RemoveAlertFiltersFromGroupsJob;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use App\Models\User;
use App\Settings\LicenseSettings;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    DB::statement('DROP VIEW IF EXISTS student_alerts');
    $settings = app(LicenseSettings::class);

    $settings->data->addons->earlyAlert = true;
    $settings->save();
});

it('is gated with proper access control', function (): void {
    $user = User::factory()->create();
    $settings = app(LicenseSettings::class);

    $settings->data->addons->earlyAlert = false;
    $settings->save();

    actingAs($user);

    get(ManageAlerts::getUrl())->assertForbidden();

    $settings->data->addons->earlyAlert = true;
    $settings->save();

    get(ManageAlerts::getUrl())->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ManageAlerts::getUrl())->assertSuccessful();
});

$getTabLabels = function (Testable $component): array {
    $document = new DOMDocument();
    $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $tabs = (new DOMXPath($document))->query('//*[@role="tab"]');
    assert($tabs !== false);

    $labels = [];

    foreach ($tabs as $tab) {
        $labels[] = trim((string) preg_replace('/\s+/', ' ', $tab->textContent));
    }

    return $labels;
};

describe('tabs and authorization', function () use ($getTabLabels): void {
    it('shows the Student Alerts tab to a user who can view settings', function () use ($getTabLabels): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        expect($getTabLabels(Livewire::test(ManageAlerts::class)))->toBe(['Student Alerts']);
    });

    it('returns 403 when the Early Alert feature is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->earlyAlert = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(ManageAlerts::getUrl())->assertForbidden();
    });

    it('returns 403 when no tab is visible', function (): void {
        $user = User::factory()->create();
        actingAs($user);

        get(ManageAlerts::getUrl())->assertForbidden();
    });

    it('opens the first visible tab when the requested tab is unknown', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        Livewire::withQueryParams(['tab' => 'does-not-exist'])
            ->test(ManageAlerts::class)
            ->assertSet('activeTab', EarlyAlertTab::StudentAlerts->value);
    });

    it('keeps the requested tab when it is visible', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        Livewire::withQueryParams(['tab' => EarlyAlertTab::StudentAlerts->value])
            ->test(ManageAlerts::class)
            ->assertSet('activeTab', EarlyAlertTab::StudentAlerts->value);
    });
});

describe('view-only regression', function (): void {
    beforeEach(function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);
    });

    it('disables the form for a user who cannot update settings', function (): void {
        $alertConfig = AlertConfiguration::factory()
            ->state(['preset' => AlertPreset::DorfGrade])
            ->create();

        Livewire::test(ManageAlerts::class)
            ->assertFormFieldDisabled("{$alertConfig->id}.is_enabled");
    });

    it('hides the save button for a user who cannot update settings', function (): void {
        Livewire::test(ManageAlerts::class)
            ->assertDontSeeText('Save Changes');
    });

    it('rejects the save method for a user who cannot update settings', function (): void {
        $alertConfig = AlertConfiguration::factory()
            ->state(['preset' => AlertPreset::DorfGrade])
            ->enabled()
            ->create();

        Livewire::test(ManageAlerts::class)
            ->set('data', [(string) $alertConfig->id => ['is_enabled' => false]])
            ->call('save')
            ->assertForbidden();

        $alertConfig->refresh();
        expect($alertConfig->is_enabled)->toBeTrue();
    });

    it('rejects the confirm disable alerts action for a user who cannot update settings', function (): void {
        Livewire::test(ManageAlerts::class)
            ->mountAction('confirmDisableAlerts')
            ->callMountedAction()
            ->assertForbidden();
    });
});

it('shows the save button to a user who can update settings', function (): void {
    asSuperAdmin();

    Livewire::test(ManageAlerts::class)
        ->assertSeeText('Save Changes');
});

it('saves without showing modal when no alerts are being disabled', function (): void {
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

it('saves without showing modal when disabling alerts not used by any groups', function (): void {
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

it('shows confirmation modal when disabling alerts used by groups', function (): void {
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

it('dispatches job when proceeding with disabling alerts used by groups', function (): void {
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

    Queue::assertPushed(RemoveAlertFiltersFromGroupsJob::class, function (RemoveAlertFiltersFromGroupsJob $job) use ($alertConfig): bool {
        return in_array((string) $alertConfig->id, $job->alertConfigurationIds, true);
    });

    $alertConfig->refresh();
    expect($alertConfig->is_enabled)->toBeFalse();
});

it('does not dispatch job when save proceeds without modal', function (): void {
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

it('detects multiple alerts being disabled that are used by different groups', function (): void {
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
