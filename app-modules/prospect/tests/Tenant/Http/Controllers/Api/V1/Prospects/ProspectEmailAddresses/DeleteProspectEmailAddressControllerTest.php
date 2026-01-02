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
