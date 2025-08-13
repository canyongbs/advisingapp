<?php

use AdvisingApp\Authorization\Models\LoginMagicLink;
use AdvisingApp\Authorization\Tests\Tenant\Http\Controllers\RequestFactories\GenerateLoginMagicLinkRequestFactory;
use App\Http\Middleware\CheckOlympusKey;
use App\Models\Authenticatable;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

it('requires Olympus Key authentication', function () {
    post(
        route('magic-link.generate'),
        [
            'email' => fake()->safeEmail(),
            'name' => fake()->name(),
            'type' => Authenticatable::SUPER_ADMIN_ROLE,
        ]
    )
        ->assertForbidden();
});

it('can generate a login magic link for a non-existing user', function () {
    $email = fake()->safeEmail();
    $name = fake()->name();

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('magic-link.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link']);

    // Verify that the user was created
    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toEqual($name)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(LoginMagicLink::class, 1);

    $magicLink = LoginMagicLink::first();

    expect($magicLink->user_id)->toEqual($user->id);

    // TODO: Test that the link parameter decrypts to what we expect
});

it('can generate a login magic link for an existing user', function () {
    $user = User::factory()->create();

    $email = $user->email;
    $name = $user->name;

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('magic-link.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(LoginMagicLink::class, 1);

    $magicLink = LoginMagicLink::first();

    expect($magicLink->user_id)->toEqual($user->id);

    // TODO: Test that the link parameter decrypts to what we expect
});

it('can generate a login magic link for an existing user that is deleted', function () {
    $user = User::factory()->create();

    $email = $user->email;
    $name = $user->name;

    $user->delete();

    expect($user->trashed())->toBeTrue();

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('magic-link.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->trashed())->toBeFalse()
        ->and($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(LoginMagicLink::class, 1);

    $magicLink = LoginMagicLink::first();

    expect($magicLink->user_id)->toEqual($user->id);

    // TODO: Test that the link parameter decrypts to what we expect
});

it('updates details of an existing user', function () {
    $user = User::factory()->create(
        [
            'is_external' => false,
            'email_verified_at' => null,
        ]
    );

    $email = $user->email;
    $name = fake()->name();

    expect($user->name)->not->toBe($name);

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('magic-link.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(LoginMagicLink::class, 1);

    $magicLink = LoginMagicLink::first();

    expect($magicLink->user_id)->toEqual($user->id);

    // TODO: Test that the link parameter decrypts to what we expect
});

it('deletes existing magic links for a user', function () {
    $user = User::factory()->create();

    $email = $user->email;
    $name = $user->name;

    $existingMagicLink = LoginMagicLink::factory()->create(['user_id' => $user->id]);

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('magic-link.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(LoginMagicLink::class, 1);

    expect($existingMagicLink->fresh())->toBeNull();

    $magicLink = LoginMagicLink::first();

    expect($magicLink->user_id)->toEqual($user->id);

    // TODO: Test that the link parameter decrypts to what we expect
});

it('requires valid data', function (GenerateLoginMagicLinkRequestFactory $data, array $errors) {
    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('magic-link.generate'),
            GenerateLoginMagicLinkRequestFactory::new($data)->create()
        )
        ->assertInvalid($errors);

    assertDatabaseCount(LoginMagicLink::class, 0);
})
    ->with(
        [
            'email required' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['email' => null]),
                ['email' => 'required'],
            ],
            'email must be valid email' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['email' => 'invalid-email']),
                ['email' => 'email'],
            ],
            'name required' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['name' => null]),
                ['name' => 'required'],
            ],
            'name string' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['name' => 1]),
                ['name' => 'string'],
            ],
            'name max' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['name' => str()->random(256)]),
                ['name' => ['The name may not be greater than 255 characters.']],
            ],
            'type required' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['type' => null]),
                ['type' => 'required'],
            ],
            'type must be correct value' => [
                GenerateLoginMagicLinkRequestFactory::new()->state(['type' => 'invalid']),
                ['type' => 'in'],
            ],
        ],
    );
