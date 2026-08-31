<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Authorization\Models\OneTimeLoginCode;
use AdvisingApp\Authorization\Tests\Tenant\Http\Controllers\Api\RequestFactories\CreateOneTimeLoginUrlRequestFactory;
use App\Http\Middleware\CheckOlympusKey;
use App\Models\User;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\freezeTime;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;

it('creates and emails a signed one-time login link', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $user = User::factory()->create();

    $link = postJson(
        route('api.one-time-login.url'),
        CreateOneTimeLoginUrlRequestFactory::new()->state(['email' => $user->email])->create(),
    )
        ->assertOk()
        ->assertJsonStructure(['link'])
        ->json('link');

    expect($link)->toContain('auth/login')
        ->and(URL::hasValidSignature(request()->create($link), absolute: false))->toBeTrue()
        ->and(OneTimeLoginCode::query()->where('user_id', $user->getKey())->count())->toBe(1);
});

it('returns a link that expires after 30 minutes', function () {
    withoutMiddleware(CheckOlympusKey::class);

    freezeTime();

    $user = User::factory()->create();

    $link = postJson(
        route('api.one-time-login.url'),
        CreateOneTimeLoginUrlRequestFactory::new()->state(['email' => $user->email])->create(),
    )
        ->assertOk()
        ->json('link');

    parse_str((string) parse_url((string) $link, PHP_URL_QUERY), $query);

    expect((int) $query['expires'])->toBe(now()->addMinutes(30)->timestamp);
});

it('validates the inputs', function (array $overrides, array $errors) {
    withoutMiddleware(CheckOlympusKey::class);

    $user = User::factory()->create();

    $request = CreateOneTimeLoginUrlRequestFactory::new()
        ->state(['email' => $user->email])
        ->state($overrides)
        ->create();

    postJson(
        route('api.one-time-login.url'),
        $request,
    )
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(OneTimeLoginCode::count())->toBe(0);
})->with([
    'email is required' => fn () => [['email' => ''], ['email']],
    'email must be a valid address' => fn () => [['email' => 'not-an-email'], ['email']],
    'email must belong to an existing user' => fn () => [['email' => 'unknown@example.com'], ['email']],
]);

it('does not create a link for a soft-deleted user', function () {
    withoutMiddleware(CheckOlympusKey::class);

    $user = User::factory()->create();

    $user->delete();

    expect($user->trashed())->toBeTrue();

    postJson(
        route('api.one-time-login.url'),
        ['email' => $user->email],
    )
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);

    expect(OneTimeLoginCode::count())->toBe(0);
});

describe('authorization', function () {
    it('rejects requests without a valid Olympus key', function () {
        postJson(
            route('api.one-time-login.url'),
            CreateOneTimeLoginUrlRequestFactory::new()->create(),
        )
            ->assertForbidden();
    });
});
