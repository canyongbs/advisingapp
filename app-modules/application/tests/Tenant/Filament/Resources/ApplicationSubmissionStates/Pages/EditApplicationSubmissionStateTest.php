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

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStates\Pages\EditApplicationSubmissionState;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('can load edit page for a submission state', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->assertSuccessful();
});

test('view action is available on edit page', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->assertActionExists('view');
});

test('archive action is visible on edit page when state is not archived', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->assertActionVisible('archive');
});

test('archive action archives the state and redirects to index', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->callAction('archive');

    expect($state->fresh()->isArchived())->toBeTrue();
});

test('delete action is visible when state has no associated submissions', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Deny,
    ]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->assertActionVisible(DeleteAction::class);
});

test('delete action is hidden when state has associated submissions', function () {
    asSuperAdmin();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    ApplicationSubmission::factory()->create(['state_id' => $state->id]);

    livewire(EditApplicationSubmissionState::class, ['record' => $state->getRouteKey()])
        ->assertActionHidden(DeleteAction::class);
});

test('archive policy requires settings delete permission', function () {
    $user = User::factory()->create();

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    expect($user->can('archive', $state))->toBeFalse();

    $user->givePermissionTo('settings.*.delete');

    expect($user->can('archive', $state))->toBeTrue();
});

test('delete policy denies when state has submissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.*.delete');

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    ApplicationSubmission::factory()->create(['state_id' => $state->id]);

    expect($user->can('delete', $state))->toBeFalse();
});

test('delete policy allows when state has no submissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.*.delete');

    $state = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Deny,
    ]);

    expect($user->can('delete', $state))->toBeTrue();
});
