<?php

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
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData);

    // $prospectConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    // $prospectConfigurationSettings->is_enabled = true;
    // $prospectConfigurationSettings->save();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.email-addresses.update', ['prospect' => $prospect, 'prospectEmailAddress' => $prospectEmailAddress], false), $updateProspectEmailAddressRequestData)
        ->assertOk();
});

it('updates a prospect', function () {
    // $prospectConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    // $prospectConfigurationSettings->is_enabled = true;
    // $prospectConfigurationSettings->save();

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
    // $prospectConfigurationSettings = app(ManageStudentConfigurationSettings::class);
    // $prospectConfigurationSettings->is_enabled = true;
    // $prospectConfigurationSettings->save();

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
