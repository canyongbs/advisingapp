<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $prospect = Prospect::factory()->create();
    $prospectEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.email-addresses.delete', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.email-addresses.delete', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.update');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.email-addresses.delete', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.email-addresses.delete', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false))
        ->assertNoContent();
});

it('deletes a prospect email address', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();
    $prospectEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();

    $response = deleteJson(route('api.v1.prospects.email-addresses.delete', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false));
    $response->assertNoContent();

    assertDatabaseMissing(ProspectEmailAddress::class, [
        'id' => $prospectEmailAddress->getKey(),
    ]);
});

it('swaps out the current primary email address for a prospect with another when the primary email address is deleted', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();
    $secondaryEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();

    $response = deleteJson(route('api.v1.prospects.email-addresses.delete', ['prospect' => $prospect, 'prospectEmailAddress' => $prospect->primaryEmailAddress], false));
    $response->assertNoContent();

    assertDatabaseMissing(ProspectEmailAddress::class, [
        'id' => $prospect->primaryEmailAddress->getKey(),
    ]);

    expect($prospect->refresh()->primaryEmailAddress()->is($secondaryEmailAddress))
        ->toBeTrue();
});
