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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\Scopes\InteractionConfidentialScope;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('Interaction model has applied global scope', function () {
    Interaction::factory()->create();

    expect(Interaction::hasGlobalScope(InteractionConfidentialScope::class))->toBeTrue();
});

test('Interactions model with fetch data for created user', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);
    $ownedConfidentialInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
        'user_id' => $user,
    ]);

    $privateInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $interactions = Interaction::query()->get();

    expect($interactions)->toHaveCount(20);

    expect($interactions->pluck('id'))
        ->toContain(...$publicInteractions->pluck('id'))
        ->toContain(...$ownedConfidentialInteractions->pluck('id'));

    expect($interactions->pluck('id'))->not->toContain(...$privateInteractions->pluck('id'));

    expect($interactions->where('is_confidential', true)->pluck('user_id'))
        ->not->toContain(...$privateInteractions->pluck('user_id'));
});

test('Interactions model with fetch data for team user', function () {
    $teamUser = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->hasAttached($teamUser, [], 'users')->create();

    actingAs($teamUser);

    $ownedConfidentialInteractions = Interaction::factory()->hasAttached($team, [], 'confidentialAccessTeams')->count(10)->create([
        'is_confidential' => true,
    ]);

    $privateInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $interactions = Interaction::query()->get();

    expect($interactions)->toHaveCount(20);

    expect($interactions->pluck('id'))
        ->toContain(...$publicInteractions->pluck('id'))
        ->toContain(...$ownedConfidentialInteractions->pluck('id'));

    expect($interactions->pluck('id'))->not->toContain(...$privateInteractions->pluck('id'));

    expect($interactions->where('is_confidential', true)->pluck('user_id'))
        ->not->toContain(...$privateInteractions->pluck('user_id'));
});

test('Interactions model with fetch data for assigned user', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $ownedConfidentialInteractions = Interaction::factory()->hasAttached($user, [], 'confidentialAccessUsers')->count(10)->create([
        'is_confidential' => true,
    ]);

    $privateInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $interactions = Interaction::query()->get();

    expect($interactions)->toHaveCount(20);

    expect($interactions->pluck('id'))
        ->toContain(...$publicInteractions->pluck('id'))
        ->toContain(...$ownedConfidentialInteractions->pluck('id'));

    expect($interactions->pluck('id'))->not->toContain(...$privateInteractions->pluck('id'));

    expect($interactions->where('is_confidential', true)->pluck('user_id'))
        ->not->toContain(...$privateInteractions->pluck('user_id'));
});

test('Interactions model with fetch data for superadmin user', function () {
    asSuperAdmin();

    $privateInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicInteractions = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $interactions = Interaction::query()->get();

    expect($interactions)->toHaveCount(20);

    expect($interactions->pluck('id'))
        ->toContain(...$publicInteractions->pluck('id'))
        ->toContain(...$privateInteractions->pluck('id'));
});
