<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\IntegrationTwilio\Http\Middleware\LogTelnyxRequest;
use AdvisingApp\Webhook\Actions\StoreInboundWebhook;
use AdvisingApp\Webhook\Enums\InboundWebhookSource;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('will create an inbound webhook with the correct source and event for a Telnyx webhook', function (string $event) {
    $request = new Request(
        server: ['REQUEST_URI' => 'testing'],
        content: json_encode([
            'data' => [
                'event_type' => $event,
                'id' => 'fake-id-123',
                'occurred_at' => now()->toISOString(),
                'payload' => [
                    'from' => [
                        'phone_number' => '+1234567890',
                    ],
                    'to' => [
                        [
                            'phone_number' => '+0987654321',
                        ],
                    ],
                    'text' => 'Test message content',
                ],
                // ... Note: this is not an example of a complete payload
            ],
        ])
    );

    $request->setRouteResolver(function () use ($request) {
        return (new Route('POST', 'testing', []))->bind($request);
    });

    $middleware = new LogTelnyxRequest(app(StoreInboundWebhook::class));

    $middleware->handle($request, function (Request $request) {
        return response()->json();
    });

    assertDatabaseCount('inbound_webhooks', 1);

    assertDatabaseHas('inbound_webhooks', [
        'source' => InboundWebhookSource::Telnyx->value,
        'event' => $event,
        'url' => $request->url(),
        'payload' => $request->getContent(),
    ]);
})->with([
    'message.finalized',
    'message.sent',
    'message.received',
]);
