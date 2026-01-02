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
use AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses\RequestFactories\UpdateProspectEmailAddressRequestFactory;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $prospect = Prospect::factory()->create();
    $prospectEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();
    $updateProspectEmailAddressRequestData = UpdateProspectEmailAddressRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData)->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData)->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData)->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData)
        ->assertOk();
});

it('updates a prospect', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();
    $prospectEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();
    $updateProspectEmailAddressRequestData = UpdateProspectEmailAddressRequestFactory::new()->create();

    $response = patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    if (isset($updateProspectEmailAddressRequestData['address'])) {
        expect($response['data']['address'] ?? null)
            ->toBe($updateProspectEmailAddressRequestData['address']);
    }

    if (isset($updateProspectEmailAddressRequestData['type'])) {
        expect($response['data']['type'] ?? null)
            ->toBe($updateProspectEmailAddressRequestData['type']);
    }

    if (isset($updateProspectEmailAddressRequestData['order'])) {
        expect($response['data']['order'] ?? null)
            ->toBe($updateProspectEmailAddressRequestData['order']);
    }
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $prospect = Prospect::factory()->create();
    $prospectEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();
    $updateProspectEmailAddressRequestData = UpdateProspectEmailAddressRequestFactory::new()->create($requestAttributes);

    $response = patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`address` is a valid email' => [['address' => 'not-an-email'], 'address', 'The address must be a valid email address.'],
    '`type` is max 255 characters' => [['type' => str_repeat('a', 256)], 'type', 'The type may not be greater than 255 characters.'],
    '`order` is integer' => [['order' => 'not-an-integer'], 'order', 'The order must be an integer.'],
]);
