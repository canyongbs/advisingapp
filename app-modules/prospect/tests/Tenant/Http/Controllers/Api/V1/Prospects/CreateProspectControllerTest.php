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
use AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses\RequestFactories\CreateProspectEmailAddressRequestFactory;
use AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\RequestFactories\CreateProspectRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $createProspectRequestData = CreateProspectRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.create');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.create']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData)
        ->assertCreated();
});

it('creates a prospect', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.create']);
    Sanctum::actingAs($user, ['api']);

    $createProspectRequestData = collect(CreateProspectRequestFactory::new()->create());

    $response = postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData->toArray());
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['first_name'] ?? null)
        ->toBe($createProspectRequestData['first_name']);

    expect($response['data']['last_name'] ?? null)
        ->toBe($createProspectRequestData['last_name']);

    expect($response['data']['full_name'] ?? null)
        ->toBe($createProspectRequestData['full_name']);

    if (isset($createProspectRequestData['preferred'])) {
        expect($response['data']['preferred'] ?? null)
            ->toBe($createProspectRequestData['preferred']);
    }

    if (isset($createProspectRequestData['description'])) {
        expect($response['data']['description'] ?? null)
            ->toBe($createProspectRequestData['description']);
    }

    expect($response['data']['status'] ?? null)
        ->toBe($createProspectRequestData['status']);

    expect($response['data']['source'] ?? null)
        ->toBe($createProspectRequestData['source']);

    if (isset($createProspectRequestData['birthdate'])) {
        expect($response['data']['birthdate'] ?? null)
            ->toBe($createProspectRequestData['birthdate']);
    }

    if (isset($createProspectRequestData['hsgrad'])) {
        expect($response['data']['hsgrad'] ?? null)
            ->toBe($createProspectRequestData['hsgrad']);
    }
    assertDatabaseHas(
        Prospect::class,
        $createProspectRequestData
            ->except(['status', 'source'])
            ->toArray()
    );
});

it('creates prospect email addresses', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.create']);
    Sanctum::actingAs($user, ['api']);

    $createProspectRequestData = CreateProspectRequestFactory::new()->create([
        'email_addresses' => [
            CreateProspectEmailAddressRequestFactory::new()->create(),
            CreateProspectEmailAddressRequestFactory::new()->create(),
        ],
    ]);

    $response = postJson(route('api.v1.prospects.create', ['include' => 'email_addresses,primary_email_address'], false), $createProspectRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['email_addresses'] ?? null)
        ->toBeArray()
        ->toHaveCount(2);

    expect($response['data']['email_addresses'][0]['address'] ?? null)
        ->toBe($createProspectRequestData['email_addresses'][0]['address']);

    if (isset($createProspectRequestData['email_addresses'][0]['type'])) {
        expect($response['data']['email_addresses'][0]['type'] ?? null)
            ->toBe($createProspectRequestData['email_addresses'][0]['type']);
    }

    expect($response['data']['email_addresses'][1]['address'] ?? null)
        ->toBe($createProspectRequestData['email_addresses'][1]['address']);

    if (isset($createProspectRequestData['email_addresses'][1]['type'])) {
        expect($response['data']['email_addresses'][1]['type'] ?? null)
            ->toBe($createProspectRequestData['email_addresses'][1]['type']);
    }

    expect($response['data']['primary_email_id'])
        ->toBe($response['data']['email_addresses'][0]['id']);

    expect($response['data']['primary_email_address'] ?? null)
        ->toBeArray();

    expect($response['data']['primary_email_address']['address'] ?? null)
        ->toBe($response['data']['email_addresses'][0]['address']);

    if (isset($createProspectRequestData['email_addresses'][0]['type'])) {
        expect($response['data']['primary_email_address']['type'] ?? null)
            ->toBe($createProspectRequestData['email_addresses'][0]['type']);
    }
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.create']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $createProspectRequestData = CreateProspectRequestFactory::new()->create([
        'email_addresses' => [
            CreateProspectEmailAddressRequestFactory::new()->create(),
        ],
        ...$requestAttributes,
    ]);

    $response = postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`first_name` is required' => [['first_name' => null], 'first_name', 'The first name field is required.'],
    '`first_name` is max 255 characters' => [['first_name' => str_repeat('a', 256)], 'first_name', 'The first name may not be greater than 255 characters.'],
    '`last_name` is required' => [['last_name' => null], 'last_name', 'The last name field is required.'],
    '`last_name` is max 255 characters' => [['last_name' => str_repeat('a', 256)], 'last_name', 'The last name may not be greater than 255 characters.'],
    '`full_name` is required' => [['full_name' => null], 'full_name', 'The full name field is required.'],
    '`full_name` is max 255 characters' => [['full_name' => str_repeat('a', 256)], 'full_name', 'The full name may not be greater than 255 characters.'],
    '`preferred` is max 255 characters' => [['preferred' => str_repeat('a', 256)], 'preferred', 'The preferred may not be greater than 255 characters.'],
    '`description` is max 65535 characters' => [['description' => str_repeat('a', 65536)], 'description', 'The description may not be greater than 65535 characters.'],
    '`status` is required' => [['status' => null], 'status', 'The status field is required.'],
    '`source` is required' => [['source' => null], 'source', 'The source field is required.'],
    '`birthdate` is a valid date' => [['birthdate' => 'not-a-date'], 'birthdate', 'The birthdate is not a valid date.'],
    '`birthdate` is Y-m-d format' => [['birthdate' => '2020/01/01'], 'birthdate', 'The birthdate does not match the format Y-m-d.'],
    '`hsgrad` is numeric' => [['hsgrad' => 'not-a-number'], 'hsgrad', 'The hsgrad must be a number.'],
    '`email_addresses` is an array' => [['email_addresses' => 'not-an-array'], 'email_addresses', 'The email addresses must be an array.'],
    '`email_addresses.*` is an array' => [['email_addresses' => ['not-an-array']], 'email_addresses.0', 'The email_addresses.0 must be an array.'],
    '`email_addresses.*.address` is required' => [['email_addresses' => [['address' => null]]], 'email_addresses.0.address', 'The email_addresses.0.address field is required.'],
    '`email_addresses.*.address` is a valid email' => [['email_addresses' => [['address' => 'not-an-email']]], 'email_addresses.0.address', 'The email_addresses.0.address must be a valid email address.'],
    '`email_addresses.*.type` is max 255 characters' => [['email_addresses' => [['address' => 'test@example.com', 'type' => str_repeat('a', 256)]]], 'email_addresses.0.type', 'The email_addresses.0.type may not be greater than 255 characters.'],
]);

it('can include related prospect relationships', function (string $relationship, string $responseKey) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.create']);
    Sanctum::actingAs($user, ['api']);

    $createProspectRequestData = CreateProspectRequestFactory::new()->create();

    $response = postJson(route('api.v1.prospects.create', [], false), $createProspectRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->not()->toHaveKey($responseKey);

    $createProspectRequestData = CreateProspectRequestFactory::new()->create();

    $response = postJson(route('api.v1.prospects.create', ['include' => $relationship], false), $createProspectRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
