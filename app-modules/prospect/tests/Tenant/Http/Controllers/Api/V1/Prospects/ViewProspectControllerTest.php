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
    $prospect = Prospect::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.prospects.view', ['prospect' => $prospect], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.prospects.view', ['prospect' => $prospect], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.prospects.view', ['prospect' => $prospect], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.prospects.view', ['prospect' => $prospect], false))
        ->assertOk();
});

it('returns a prospect resource', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.view']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();

    $response = getJson(route('api.v1.prospects.view', ['prospect' => $prospect], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['id'])
        ->toBe($prospect->id);
});

it('can include related prospect relationships', function (string $relationship, string $responseKey) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.view']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();

    $response = getJson(route('api.v1.prospects.view', ['prospect' => $prospect], false));
    $response->assertOk();

    expect($response['data'])
        ->not()->toHaveKey($responseKey);

    $response = getJson(route('api.v1.prospects.view', [$prospect, 'include' => $relationship], false));
    $response->assertOk();

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`emailAddresses`' => ['email_addresses', 'email_addresses'],
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
