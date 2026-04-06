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

use AdvisingApp\Notification\Http\Controllers\UnsubscribeController;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('GET with a valid signed URL returns 200 and shows the unsubscribe confirmation page', function () {
    $email = 'user@example.com';

    $url = URL::signedRoute('unsubscribe', ['email' => $email]);

    get($url)
        ->assertStatus(200)
        ->assertSee('Are you sure you want to unsubscribe')
        ->assertSee('Unsubscribe');
});

it('GET with a missing or invalid signature returns 403', function () {
    get('/unsubscribe?email=user@example.com')
        ->assertStatus(403);
});

it('POST with a valid signed URL creates an EmailAddressOptInOptOut record with OptedOut status and shows success page', function () {
    $email = 'user@example.com';

    $url = URL::signedRoute('unsubscribe.store', ['email' => $email]);

    post($url)
        ->assertStatus(200)
        ->assertSee('You have been successfully unsubscribed.');

    $record = EmailAddressOptInOptOut::where('address', $email)->first();

    expect($record)->not->toBeNull()
        ->and($record->status)->toBe(EmailAddressOptInOptOutStatus::OptedOut);
});

it('POST is idempotent and does not error on double submission leaving only one record', function () {
    $email = 'user@example.com';

    $url = URL::signedRoute('unsubscribe.store', ['email' => $email]);

    post($url)->assertStatus(200);
    post($url)->assertStatus(200);

    expect(EmailAddressOptInOptOut::where('address', $email)->count())->toBe(1)
        ->and(EmailAddressOptInOptOut::where('address', $email)->first()->status)->toBe(EmailAddressOptInOptOutStatus::OptedOut);
});

it('store method returns 204 for HEAD request for bot protection without creating a record', function () {
    $email = 'user@example.com';

    $request = Request::create('/unsubscribe?email=' . $email, 'HEAD');

    $controller = app(UnsubscribeController::class);
    $response = $controller->store($request);

    expect($response->getStatusCode())->toBe(204)
        ->and(EmailAddressOptInOptOut::where('address', $email)->exists())->toBeFalse();
});

it('POST with a missing or invalid signature returns 403', function () {
    post('/unsubscribe?email=user@example.com')
        ->assertStatus(403);
});
