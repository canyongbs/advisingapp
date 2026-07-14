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

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationSubmissions;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStates\Pages\CreateApplicationSubmissionState;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStates\Pages\EditApplicationSubmissionState;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmissionState;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('can create a submission state with is_default set to true', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->make([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => true,
    ]);

    livewire(CreateApplicationSubmissionState::class)
        ->fillForm($state->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ApplicationSubmissionState::class, [
        'name' => $state->name,
        'is_default' => true,
    ]);
});

test('creating a state with is_default true clears the previous default', function () {
    asSuperAdmin();

    $existingDefault = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => true,
    ]);

    $newDefault = ApplicationSubmissionState::factory()->make([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => true,
    ]);

    livewire(CreateApplicationSubmissionState::class)
        ->fillForm($newDefault->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    expect($existingDefault->fresh()->is_default)->toBeFalse();

    assertDatabaseHas(ApplicationSubmissionState::class, [
        'name' => $newDefault->name,
        'is_default' => true,
    ]);
});

test('editing a state to set is_default true clears the previous default', function () {
    asSuperAdmin();

    $existingDefault = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => true,
    ]);

    $anotherState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => false,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $anotherState->getRouteKey()])
        ->fillForm(['is_default' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($existingDefault->fresh()->is_default)->toBeFalse();
    expect($anotherState->fresh()->is_default)->toBeTrue();
});

test('there can be no default state (all is_default false)', function () {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->count(3)->create(['is_default' => false]);

    expect(ApplicationSubmissionState::query()->where('is_default', true)->exists())->toBeFalse();
});

test('getDefaultActiveTab returns the default state id when one exists', function () {
    asSuperAdmin();

    $defaultState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => true,
    ]);

    $application = Application::factory()->create();

    $component = livewire(ManageApplicationSubmissions::class, ['record' => $application->getRouteKey()])
        ->instance();

    expect($component->getDefaultActiveTab())->toBe($defaultState->id);
});

test('getDefaultActiveTab returns all when no default state is set', function () {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->count(2)->create(['is_default' => false]);

    $application = Application::factory()->create();

    $component = livewire(ManageApplicationSubmissions::class, ['record' => $application->getRouteKey()])
        ->instance();

    expect($component->getDefaultActiveTab())->toBe('all');
});

test('is_default toggle is visible on create form', function () {
    asSuperAdmin();

    livewire(CreateApplicationSubmissionState::class)
        ->assertFormFieldExists('is_default');
});

test('is_default toggle is visible on edit form', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->assertFormFieldExists('is_default');
});
