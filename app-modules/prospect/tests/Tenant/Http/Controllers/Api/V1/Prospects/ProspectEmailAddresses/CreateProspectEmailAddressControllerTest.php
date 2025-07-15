<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses\RequestFactories\CreateProspectEmailAddressRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $prospect = Prospect::factory()->create();
    $createProspectEmailAddressRequestData = CreateProspectEmailAddressRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.email-addresses.create', ['prospect' => $prospect], false), $createProspectEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.email-addresses.create', ['prospect' => $prospect], false), $createProspectEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.update');
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.email-addresses.create', ['prospect' => $prospect], false), $createProspectEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);
    postJson(route('api.v1.prospects.email-addresses.create', ['prospect' => $prospect], false), $createProspectEmailAddressRequestData)
        ->assertCreated();
});

it('creates a prospect email address', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();
    $createProspectEmailAddressRequestData = CreateProspectEmailAddressRequestFactory::new()->create();

    $response = postJson(route('api.v1.prospects.email-addresses.create', ['prospect' => $prospect], false), $createProspectEmailAddressRequestData);
    $response->assertCreated();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['prospect_id'] ?? null)
        ->toBe($prospect->id);

    expect($response['data']['address'] ?? null)
        ->toBe($createProspectEmailAddressRequestData['address']);

    if (isset($createProspectEmailAddressRequestData['type'])) {
        expect($response['data']['type'] ?? null)
            ->toBe($createProspectEmailAddressRequestData['type']);
    }

    if (isset($createProspectEmailAddressRequestData['order'])) {
        expect($response['data']['order'] ?? null)
            ->toBe($createProspectEmailAddressRequestData['order']);
    }

    assertDatabaseHas(ProspectEmailAddress::class, [
        'prospect_id' => $prospect->id,
        'address' => $createProspectEmailAddressRequestData['address'],
    ]);
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $prospect = Prospect::factory()->create();
    $createProspectEmailAddressRequestData = CreateProspectEmailAddressRequestFactory::new()->create($requestAttributes);

    $response = postJson(route('api.v1.prospects.email-addresses.create', ['prospect' => $prospect], false), $createProspectEmailAddressRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`address` is required' => [['address' => null], 'address', 'The address field is required.'],
    '`address` is a valid email' => [['address' => 'not-an-email'], 'address', 'The address must be a valid email address.'],
    '`type` is max 255 characters' => [['type' => str_repeat('a', 256)], 'type', 'The type may not be greater than 255 characters.'],
    '`order` is integer' => [['order' => 'not-an-integer'], 'order', 'The order must be an integer.'],
]);
