<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Tests\Tenant\Http\Controllers\Api\V1\Prospects\RequestFactories\UpdateProspectRequestFactory;
use App\Models\SystemUser;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $prospect = Prospect::factory()->create();
    $updateProspectRequestData = UpdateProspectRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), $updateProspectRequestData)->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), $updateProspectRequestData)->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('prospect.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), $updateProspectRequestData)->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), $updateProspectRequestData)
        ->assertOk();
});

it('updates a prospect', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();
    $updateProspectRequestData = UpdateProspectRequestFactory::new()->create();

    $response = patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), $updateProspectRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    if (isset($updateProspectRequestData['first_name'])) {
        expect($response['data']['first_name'] ?? null)
            ->toBe($updateProspectRequestData['first_name']);
    }

    if (isset($updateProspectRequestData['last_name'])) {
        expect($response['data']['last_name'] ?? null)
            ->toBe($updateProspectRequestData['last_name']);
    }

    if (isset($updateProspectRequestData['full_name'])) {
        expect($response['data']['full_name'] ?? null)
            ->toBe($updateProspectRequestData['full_name']);
    }

    if (isset($updateProspectRequestData['preferred'])) {
        expect($response['data']['preferred'] ?? null)
            ->toBe($updateProspectRequestData['preferred']);
    }

    if (isset($updateProspectRequestData['description'])) {
        expect($response['data']['description'] ?? null)
            ->toBe($updateProspectRequestData['description']);
    }

    if (isset($updateProspectRequestData['sms_opt_out'])) {
        expect($response['data']['sms_opt_out'] ?? null)
            ->toBe($updateProspectRequestData['sms_opt_out']);
    }

    if (isset($updateProspectRequestData['email_bounce'])) {
        expect($response['data']['email_bounce'] ?? null)
            ->toBe($updateProspectRequestData['email_bounce']);
    }

    if (isset($updateProspectRequestData['status'])) {
        expect($response['data']['status'] ?? null)
            ->toBe($updateProspectRequestData['status']);
    }

    if (isset($updateProspectRequestData['source'])) {
        expect($response['data']['source'] ?? null)
            ->toBe($updateProspectRequestData['source']);
    }

    if (isset($updateProspectRequestData['birthdate'])) {
        expect($response['data']['birthdate'] ?? null)
            ->toBe($updateProspectRequestData['birthdate']);
    }

    if (isset($updateProspectRequestData['hsgrad'])) {
        expect($response['data']['hsgrad'] ?? null)
            ->toBe($updateProspectRequestData['hsgrad']);
    }
});

it('updates a prospect\'s primary email address', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();

    $prospectEmailAddress = ProspectEmailAddress::factory()
        ->for($prospect)
        ->create();

    expect($prospect->refresh()->primary_email_id)
        ->not->toBe($prospectEmailAddress->getKey());

    $response = patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), [
        'primary_email_id' => $prospectEmailAddress->getKey(),
    ]);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['primary_email_id'] ?? null)
        ->toBe($prospectEmailAddress->getKey());
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage, ?Closure $before = null) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $before?->call($this);

    $prospect = Prospect::factory()->create();
    $updateProspectRequestData = UpdateProspectRequestFactory::new()->create($requestAttributes);

    $response = patchJson(route('api.v1.prospects.update', ['prospect' => $prospect], false), $updateProspectRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    // requestAttributes, invalidAttribute, validationMessage, before
    '`first_name` is max 255 characters' => [['first_name' => str_repeat('a', 256)], 'first_name', 'The first name may not be greater than 255 characters.'],
    '`last_name` is max 255 characters' => [['last_name' => str_repeat('a', 256)], 'last_name', 'The last name may not be greater than 255 characters.'],
    '`full_name` is max 255 characters' => [['full_name' => str_repeat('a', 256)], 'full_name', 'The full name may not be greater than 255 characters.'],
    '`preferred` is max 255 characters' => [['preferred' => str_repeat('a', 256)], 'preferred', 'The preferred may not be greater than 255 characters.'],
    '`description` is max 65535 characters' => [['description' => str_repeat('a', 65536)], 'description', 'The description may not be greater than 65535 characters.'],
    '`status` is max 255 characters' => [['status' => str_repeat('a', 256)], 'status', 'The status may not be greater than 255 characters.'],
    '`source` is max 255 characters' => [['source' => str_repeat('a', 256)], 'source', 'The source may not be greater than 255 characters.'],
    '`birthdate` is a valid date' => [['birthdate' => 'not-a-date'], 'birthdate', 'The birthdate is not a valid date.'],
    '`birthdate` is Y-m-d format' => [['birthdate' => '2020/01/01'], 'birthdate', 'The birthdate does not match the format Y-m-d.'],
    '`hsgrad` is numeric' => [['hsgrad' => 'not-a-number'], 'hsgrad', 'The hsgrad must be a number.'],
    '`sms_opt_out` is boolean' => [['sms_opt_out' => 'not-boolean'], 'sms_opt_out', 'The sms opt out field must be true or false.'],
    '`email_bounce` is boolean' => [['email_bounce' => 'not-boolean'], 'email_bounce', 'The email bounce field must be true or false.'],
    '`primary_email_id` is a valid UUID' => [['primary_email_id' => 'not-a-uuid'], 'primary_email_id', 'The primary email id must be a valid UUID.'],
    '`primary_email_id` is an existing email address ID' => [['primary_email_id' => (string) Str::orderedUuid()], 'primary_email_id', 'The selected primary email id is invalid.'],
    '`primary_email_id` is an email address ID for the current prospect' => [['primary_email_id' => ($primaryEmailId = (string) Str::orderedUuid())], 'primary_email_id', 'The selected primary email id is invalid.', function () use ($primaryEmailId) {
        ProspectEmailAddress::factory()
            ->for(Prospect::factory())
            ->create([
                'id' => $primaryEmailId,
            ]);
    }],
]);

it('can include related prospect relationships', function (string $relationship, string $responseKey) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['prospect.view-any', 'prospect.*.update']);
    Sanctum::actingAs($user, ['api']);

    $prospect = Prospect::factory()->create();
    $updateProspectRequestData = UpdateProspectRequestFactory::new()->create();

    $response = patchJson(route('api.v1.prospects.update', ['prospect' => $prospect, 'include' => $relationship], false), $updateProspectRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data'])
        ->toHaveKey($responseKey);
})->with([
    // relationship, responseKey
    '`primaryEmailAddress`' => ['primary_email_address', 'primary_email_address'],
]);
