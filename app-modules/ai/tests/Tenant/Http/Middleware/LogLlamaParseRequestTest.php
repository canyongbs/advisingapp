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

use AdvisingApp\Ai\Http\Middleware\LogLlamaParseRequest;
use AdvisingApp\Webhook\Actions\StoreInboundWebhook;
use AdvisingApp\Webhook\Enums\InboundWebhookSource;
use Illuminate\Http\Request;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('stores the webhook as an inbound webhook from LlamaParse', function (?string $eventTypeHeader, string $expectedEvent): void {
    $request = new Request(
        server: ['REQUEST_URI' => 'inbound/webhook/llamaparse'],
        content: json_encode([
            'event_id' => 'evt-123',
            'event_type' => 'parse.error',
            'timestamp' => 1753985275.1154444,
            'data' => ['id' => 'pjb-123', 'job_id' => 'pjb-123'],
        ], JSON_THROW_ON_ERROR),
    );

    $request->headers->set('Content-Type', 'application/json');

    if (filled($eventTypeHeader)) {
        $request->headers->set('X-Webhook-Event-Type', $eventTypeHeader);
    }

    assertDatabaseCount('inbound_webhooks', 0);

    (new LogLlamaParseRequest(app(StoreInboundWebhook::class)))
        ->handle($request, fn (Request $request) => response()->noContent());

    assertDatabaseCount('inbound_webhooks', 1);

    assertDatabaseHas('inbound_webhooks', [
        'source' => InboundWebhookSource::LlamaParse->value,
        'event' => $expectedEvent,
        'url' => $request->url(),
        'payload' => $request->getContent(),
    ]);
})->with([
    'event type header' => ['parse.success', 'parse.success'],
    'no event type header' => [null, 'parse.error'],
]);
