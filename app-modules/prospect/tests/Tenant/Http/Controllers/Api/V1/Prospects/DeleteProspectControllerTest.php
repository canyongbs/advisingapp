<?php

use AdvisingApp\Prospect\Models\Prospect;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $prospect = Prospect::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.delete', ['prospect' => $prospect], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.delete', ['prospect' => $prospect], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.delete');
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.delete', ['prospect' => $prospect], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.delete']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.delete', ['prospect' => $prospect], false))
        ->assertForbidden();

    // $prospectConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    // $prospectConfigurationSettings->is_enabled = true;
    // $prospectConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.delete']);
    Sanctum::actingAs($user, ['api']);
    deleteJson(route('api.v1.prospects.delete', ['prospect' => $prospect], false))
        ->assertNoContent();
});

it('deletes a prospect', function () {
    // $prospectConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    // $prospectConfigurationSettings->is_enabled = true;
    // $prospectConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.delete']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();

    $response = deleteJson(route('api.v1.prospects.delete', ['prospect' => $prospect], false));
    $response->assertNoContent();

    assertSoftDeleted(Prospect::class, [
        'sisid' => $prospect->id,
    ]);
});
