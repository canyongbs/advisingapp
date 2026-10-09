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

use App\Filament\Pages\ManageLicenseSettings;
use App\Settings\LicenseSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    config(['app.allow_license_settings_editing' => true]);

    asSuperAdmin();
});

describe('enterprise ai', function () {
    it('shows the AI fields only while Enterprise AI is enabled', function (string $field) {
        livewire(ManageLicenseSettings::class)
            ->assertFormFieldVisible($field)
            ->fillForm(['data.addons.enterpriseAi' => false])
            ->assertFormFieldHidden($field);
    })->with([
        'Artificial Intelligence Seats' => 'data.limits.conversationalAiSeats',
        'Employee Advisors count' => 'data.limits.employeeAdvisorsCount',
        'Customer Advisors count' => 'data.limits.customerAdvisorsCount',
        'Employee Advisors toggle' => 'data.addons.employeeAdvisors',
        'Customer Advisors toggle' => 'data.addons.customerAdvisors',
    ]);

    it('keeps the AI values when saved while Enterprise AI is disabled', function () {
        $licenseSettings = app(LicenseSettings::class);
        $licenseSettings->data->limits->conversationalAiSeats = 75;
        $licenseSettings->data->addons->customerAdvisors = true;
        $licenseSettings->save();

        livewire(ManageLicenseSettings::class)
            ->fillForm(['data.addons.enterpriseAi' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $licenseSettings->refresh();

        expect($licenseSettings->data->addons->enterpriseAi)->toBeFalse()
            ->and($licenseSettings->data->limits->conversationalAiSeats)->toBe(75)
            ->and($licenseSettings->data->addons->customerAdvisors)->toBeTrue();
    });
});
