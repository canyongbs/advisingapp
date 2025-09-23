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

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\Scopes\AiAssistantConfidentialScope;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('AiAssistant model has applied global scope', function () {
    AiAssistant::bootHasGlobalScopes();

    expect(AiAssistant::hasGlobalScope(AiAssistantConfidentialScope::class))->toBeTrue();
});

test('AiAssistant model with fetch data for team user', function () {
    $teamUser = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $teamUser->team()->associate($team)->save();

    $ownedConfidentialAiAssistants = AiAssistant::factory()
        ->hasAttached($team, [], 'confidentialAccessTeams')
        ->count(10)
        ->create([
            'is_confidential' => true,
        ]);

    $privateAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    actingAs($teamUser);

    $aiAssistants = AiAssistant::query()->get();

    expect($aiAssistants)->toHaveCount(20);

    expect($aiAssistants->pluck('id'))
        ->toContain(...$publicAiAssistants->pluck('id'))
        ->toContain(...$ownedConfidentialAiAssistants->pluck('id'));

    expect($aiAssistants->pluck('id'))->not->toContain(...$privateAiAssistants->pluck('id'));

    expect($aiAssistants->where('is_confidential', true)->pluck('id'))
        ->not->toContain(...$privateAiAssistants->pluck('id'));
});

test('AiAssistant model with fetch data for assigned user', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $ownedConfidentialAiAssistants = AiAssistant::factory()->hasAttached($user, [], 'confidentialAccessUsers')->count(10)->create([
        'is_confidential' => true,
    ]);

    $privateAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    actingAs($user);

    $aiAssistants = AiAssistant::query()->get();

    expect($aiAssistants)->toHaveCount(20);

    expect($aiAssistants->pluck('id'))
        ->toContain(...$publicAiAssistants->pluck('id'))
        ->toContain(...$ownedConfidentialAiAssistants->pluck('id'));

    expect($aiAssistants->pluck('id'))->not->toContain(...$privateAiAssistants->pluck('id'));

    expect($aiAssistants->where('is_confidential', true)->pluck('id'))
        ->not->toContain(...$privateAiAssistants->pluck('id'));
});

test('AiAssistant model with fetch data for creator user', function () {
    $creatorUser = User::factory()->licensed(LicenseType::cases())->create();

    $ownedConfidentialAiAssistants = AiAssistant::factory()->for($creatorUser, 'createdBy')->count(10)->create([
        'is_confidential' => true,
    ]);

    $privateAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    actingAs($creatorUser);

    $aiAssistants = AiAssistant::query()->get();

    expect($aiAssistants)->toHaveCount(20);

    expect($aiAssistants->pluck('id'))
        ->toContain(...$publicAiAssistants->pluck('id'))
        ->toContain(...$ownedConfidentialAiAssistants->pluck('id'));

    expect($aiAssistants->pluck('id'))->not->toContain(...$privateAiAssistants->pluck('id'));

    expect($aiAssistants->where('is_confidential', true)->pluck('id'))
        ->not->toContain(...$privateAiAssistants->pluck('id'));
});

test('AiAssistant model with fetch data for superadmin user', function () {
    $privateAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicAiAssistants = AiAssistant::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    asSuperAdmin();

    $aiAssistants = AiAssistant::query()->get();

    expect($aiAssistants)->toHaveCount(20);

    expect($aiAssistants->pluck('id'))
        ->toContain(...$publicAiAssistants->pluck('id'))
        ->toContain(...$privateAiAssistants->pluck('id'));
});
