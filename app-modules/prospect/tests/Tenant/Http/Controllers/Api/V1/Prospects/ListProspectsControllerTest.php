<?php

use AdvisingApp\Prospect\Models\Prospect;
use App\Models\SystemUser;
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
    '`id`' => ['id', 'ABC123', ['id' => 'ABC123'], [], 'id', 'ABC123'],
    '`first_name`' => ['first_name', 'Alice', ['first_name' => 'Alice'], ['first_name' => 'UniqueFirst'], 'first_name', 'Alice'],
    '`last_name`' => ['last_name', 'Smith', ['last_name' => 'Smith'], ['last_name' => 'UniqueLast'], 'last_name', 'Smith'],
    '`full_name`' => ['full_name', 'John Doe', ['full_name' => 'John Doe'], ['full_name' => 'Unique Name'], 'full_name', 'John Doe'],
    '`preferred`' => ['preferred', 'JD', ['preferred' => 'JD'], ['preferred' => 'UniquePref'], 'preferred', 'JD'],
    '`sms_opt_out`' => ['sms_opt_out', true, ['sms_opt_out' => true], ['sms_opt_out' => false], 'sms_opt_out', true],
    '`email_bounce`' => ['email_bounce', true, ['email_bounce' => true], ['email_bounce' => false], 'email_bounce', true],
    '`birthdate`' => ['birthdate', '2000-01-01', ['birthdate' => '2000-01-01'], ['birthdate' => '1990-01-01'], 'birthdate', '2000-01-01'],
    '`hsgrad`' => ['hsgrad', '2022', ['hsgrad' => '2022'], ['hsgrad' => '1999'], 'hsgrad', '2022'],
    '`status`' => ['status', 'A', ['status' => 'A'], ['status' => 'B'], 'status', 'A', 'B'],
    '`source`' => ['source', 'A', ['source' => 'A'], ['source' => 'B'], 'source', 'A', 'B'],
]);

dataset('sorts', [
    // requestKey, firstAttributes, secondAttributes, responseKey, responseFirstValue, responseSecondValue
    '`id`' => ['id', ['id' => 'A'], ['id' => 'B'], 'id', 'A', 'B'],
    '`first_name`' => ['first_name', ['first_name' => 'Alice'], ['first_name' => 'Bob'], 'first_name', 'Alice', 'Bob'],
    '`last_name`' => ['last_name', ['last_name' => 'Alpha'], ['last_name' => 'Zulu'], 'last_name', 'Alpha', 'Zulu'],
    '`full_name`' => ['full_name', ['full_name' => 'A'], ['full_name' => 'B'], 'full_name', 'A', 'B'],
    '`preferred`' => ['preferred', ['preferred' => 'A'], ['preferred' => 'B'], 'preferred', 'A', 'B'],
    '`sms_opt_out`' => ['sms_opt_out', true, ['sms_opt_out' => true], ['sms_opt_out' => false], 'sms_opt_out', true],
    '`email_bounce`' => ['email_bounce', true, ['email_bounce' => true], ['email_bounce' => false], 'email_bounce', true],
    '`birthdate`' => ['birthdate', ['birthdate' => '2000-01-01'], ['birthdate' => '2001-01-01'], 'birthdate', '2000-01-01', '2001-01-01'],
    '`hsgrad`' => ['hsgrad', ['hsgrad' => '2022'], ['hsgrad' => '2023'], 'hsgrad', '2022', '2023'],
    '`status`' => ['status', ['status' => 'A'], ['status' => 'B'], 'status', 'A', 'B'],
    '`source`' => ['source', ['source' => 'A'], ['source' => 'B'], 'source', 'A', 'B'],
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
