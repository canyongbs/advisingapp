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

use AdvisingApp\Authorization\Enums\AzureMatchingProperty;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Laravel\Socialite\Facades\Socialite;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\get;

use SocialiteProviders\Azure\User as AzureUser;

function fakeAzureSocialiteDriver(string $email): void
{
    $socialiteUser = new AzureUser();
    $socialiteUser->principalName = $email;
    $socialiteUser->mail = $email;
    $socialiteUser->token = 'fake-token';
    $socialiteUser->name = 'External User';
    $socialiteUser->avatar = 'https://example.com/avatar.png';

    $driver = Mockery::mock();
    $driver->shouldReceive('setConfig')->andReturnSelf(); // @phpstan-ignore method.notFound
    $driver->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->andReturn($driver);
}

beforeEach(function () {
    $settings = app(AzureSsoSettings::class);
    $settings->matching_property = AzureMatchingProperty::UserPrincipalName;
    $settings->save();

    Sleep::fake();
});

it('does not report an error when the user has no Azure profile photo', function () {
    Exceptions::fake();

    $user = User::factory()->external()->create();

    fakeAzureSocialiteDriver($user->email);

    Http::fake([
        'graph.microsoft.com/*' => Http::response([
            'error' => [
                'code' => 'ImageNotFound',
                'message' => "Exception of type 'Microsoft.People.Image.Common.Exceptions.ImageNotFoundException' was thrown.",
            ],
        ], 404),
    ]);

    get(route('socialite.callback', ['provider' => 'azure']))
        ->assertRedirect();

    assertAuthenticatedAs($user);

    Http::assertSentCount(1);

    Exceptions::assertNothingReported();

    expect($user->refresh()->getFirstMedia('avatar'))->toBeNull();
});

it('retries and reports transient Azure failures', function (int $status) {
    Exceptions::fake();

    $user = User::factory()->external()->create();

    fakeAzureSocialiteDriver($user->email);

    Http::fake([
        'graph.microsoft.com/*' => Http::response('error', $status),
    ]);

    get(route('socialite.callback', ['provider' => 'azure']))
        ->assertRedirect();

    assertAuthenticatedAs($user);

    Http::assertSentCount(3);

    Exceptions::assertReported(RequestException::class);
})->with([
    'server error' => [500],
    'rate limit' => [429],
]);
