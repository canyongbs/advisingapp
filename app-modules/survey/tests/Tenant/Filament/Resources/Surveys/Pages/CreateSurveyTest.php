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

use AdvisingApp\Survey\Filament\Resources\Surveys\Pages\CreateSurvey;
use AdvisingApp\Survey\Models\Survey;
use App\Settings\LicenseSettings;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineSurveys = true;
    $settings->save();

    asSuperAdmin();
});

it('can create a survey', function () {
    livewire(CreateSurvey::class)
        ->fillForm(['name' => 'My Survey'])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Survey::class, ['name' => 'My Survey']);
});

it('prevents creating a survey with a case-insensitively duplicate name', function () {
    Survey::factory()->create(['name' => 'Existing Survey']);

    livewire(CreateSurvey::class)
        ->fillForm(['name' => 'existing survey'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('allows reusing the name of a soft-deleted survey', function () {
    Survey::factory()->create(['name' => 'Reusable Name'])->delete();

    livewire(CreateSurvey::class)
        ->fillForm(['name' => 'reusable name'])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Survey::class, ['name' => 'reusable name']);
});
