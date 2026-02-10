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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Models\SystemUser;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.prospects.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.prospects.index', [], false))
        ->assertOk();
});

it('returns a paginated list of prospects', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);

    Prospect::factory()->count(3)->create();

    $response = getJson(route('api.v1.prospects.index', [], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data', 'links', 'meta',
    ]);

    expect($response['data'])
        ->toHaveCount(3);
});

it('can filter prospects by all attributes', function (string $requestKey, mixed $requestValue, array $includedAttributes, array $excludedAttributes, string $responseKey, mixed $responseValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);

    Prospect::factory()->create($includedAttributes);
    // Seed two prospects with the same non-matching attributes
    Prospect::factory()->create($excludedAttributes);
    Prospect::factory()->create($excludedAttributes);

    $response = getJson(route('api.v1.prospects.index', ['filter' => [$requestKey => $requestValue]], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseValue);
    expect($response['meta']['total'])
        ->toBe(1);
})->with([
    // requestKey, requestValue, includedAttributes, excludedAttributes, responseKey, responseValue
    '`id`' => [
        'id',
        (string) ($uuid = Str::uuid()),
        ['id' => $uuid],
        [],
        'id',
        (string) $uuid,
    ],
    '`first_name`' => ['first_name', 'Alice', ['first_name' => 'Alice'], ['first_name' => 'UniqueFirst'], 'first_name', 'Alice'],
    '`last_name`' => ['last_name', 'Smith', ['last_name' => 'Smith'], ['last_name' => 'UniqueLast'], 'last_name', 'Smith'],
    '`full_name`' => ['full_name', 'John Doe', ['full_name' => 'John Doe'], ['full_name' => 'Unique Name'], 'full_name', 'John Doe'],
    '`preferred`' => ['preferred', 'JD', ['preferred' => 'JD'], ['preferred' => 'UniquePref'], 'preferred', 'JD'],
    '`birthdate`' => ['birthdate', '2000-01-01', ['birthdate' => '2000-01-01'], ['birthdate' => '1990-01-01'], 'birthdate', '2000-01-01'],
    '`hsgrad`' => ['hsgrad', '2022', ['hsgrad' => '2022'], ['hsgrad' => '1999'], 'hsgrad', '2022'],
    '`status`' => [
        'status',
        'Interested',
        function () {
            $status = ProspectStatus::factory()->create(['name' => 'Interested']);

            return ['status_id' => $status->getKey()];
        },
        function () {
            $status = ProspectStatus::factory()->create(['name' => 'Not Interested']);

            return ['status_id' => $status->getKey()];
        },
        'status',
        'Interested',
    ],

    '`source`' => [
        'source',
        'Referral',
        function () {
            $source = ProspectSource::factory()->create(['name' => 'Referral']);

            return ['source_id' => $source->getKey()];
        },
        function () {
            $source = ProspectSource::factory()->create(['name' => 'Ad Campaign']);

            return ['source_id' => $source->getKey()];
        },
        'source',
        'Referral',
    ],
]);

dataset('sorts', [
    // requestKey, firstAttributes, secondAttributes, responseKey, responseFirstValue, responseSecondValue
    '`id`' => ['id', ['id' => '9f57e51c-5050-4df1-a6bc-5b0dc5e6d1d3'], ['id' => '9f592bdf-0217-4545-a2b1-0f661cb9857c'], 'id', '9f57e51c-5050-4df1-a6bc-5b0dc5e6d1d3', '9f592bdf-0217-4545-a2b1-0f661cb9857c'],
    '`first_name`' => ['first_name', ['first_name' => 'Alice'], ['first_name' => 'Bob'], 'first_name', 'Alice', 'Bob'],
    '`last_name`' => ['last_name', ['last_name' => 'Alpha'], ['last_name' => 'Zulu'], 'last_name', 'Alpha', 'Zulu'],
    '`full_name`' => ['full_name', ['full_name' => 'A'], ['full_name' => 'B'], 'full_name', 'A', 'B'],
    '`preferred`' => ['preferred', ['preferred' => 'A'], ['preferred' => 'B'], 'preferred', 'A', 'B'],
    '`birthdate`' => ['birthdate', ['birthdate' => '2000-01-01'], ['birthdate' => '2001-01-01'], 'birthdate', '2000-01-01', '2001-01-01'],
    '`hsgrad`' => ['hsgrad', ['hsgrad' => '2022'], ['hsgrad' => '2023'], 'hsgrad', '2022', '2023'],
    '`status`' => [
        'status',
        fn () => ['status_id' => ProspectStatus::factory()->create(['name' => 'Interested'])->getKey()],
        fn () => ['status_id' => ProspectStatus::factory()->create(['name' => 'Not Interested'])->getKey()],
        'status',
        'Interested',
        'Not Interested',
    ],
    '`source`' => [
        'source',
        fn () => ['source_id' => ProspectSource::factory()->create(['name' => 'Ad Campaign'])->getKey()],
        fn () => ['source_id' => ProspectSource::factory()->create(['name' => 'Referral'])->getKey()],
        'source',
        'Ad Campaign',
        'Referral',
    ],
]);

it('can sort prospects by all attributes ascending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);

    Prospect::factory()->create($firstAttributes);
    Prospect::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.prospects.index', ['sort' => $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseFirstValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseSecondValue);
})->with('sorts');

it('can sort prospects by all attributes descending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);

    Prospect::factory()->create($firstAttributes);
    Prospect::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.prospects.index', ['sort' => '-' . $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseSecondValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseFirstValue);
})->with('sorts');

it('can include related prospect relationships', function (string $relationship, string $responseKey) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);

    Prospect::factory()->create();

    $response = getJson(route('api.v1.prospects.index', [], false));
    $response->assertOk();

    expect($response['data'][0])
        ->not()->toHaveKey($responseKey);

    $response = getJson(route('api.v1.prospects.index', ['include' => $relationship], false));
    $response->assertOk();

    expect($response['data'][0])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
