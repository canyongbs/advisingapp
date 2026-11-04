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

use AdvisingApp\Ai\Actions\GetLlamaParseWebhookSigningSecret;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use App\Models\Tenant;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\call;

$sign = function (string $content, ?Tenant $tenant = null): string {
    $tenant ??= Tenant::current();

    assert($tenant instanceof Tenant);

    return 'sha256=' . hash_hmac('sha256', $content, app(GetLlamaParseWebhookSigningSecret::class)($tenant));
};

$sendWebhook = fn (string $content, ?string $signature): TestResponse => call(
    'POST',
    route('inbound.webhook.llamaparse'),
    server: array_filter([
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_WEBHOOK_EVENT_TYPE' => 'parse.success',
        'HTTP_LC_SIGNATURE' => $signature,
    ]),
    content: $content,
);

$makePayload = fn (string $parsingJobId): string => json_encode([
    'event_id' => 'evt-123',
    'event_type' => 'parse.success',
    'timestamp' => 1753985275.1154444,
    'data' => ['id' => $parsingJobId, 'job_id' => $parsingJobId],
], JSON_THROW_ON_ERROR);

it('accepts a webhook signed with the secret of the current tenant', function () use ($sign, $sendWebhook, $makePayload): void {
    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create();

    Queue::fake();

    $payload = $makePayload($link->parsing_job_id);

    $sendWebhook($payload, $sign($payload))->assertNoContent();

    assertDatabaseCount('inbound_webhooks', 1);
});

it('rejects a webhook without a signature', function () use ($sendWebhook, $makePayload): void {
    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create();

    Queue::fake();

    $sendWebhook($makePayload($link->parsing_job_id), null)->assertUnauthorized();

    Queue::assertNothingPushed();
    assertDatabaseCount('inbound_webhooks', 0);
});

it('rejects a webhook with an invalid signature', function (Closure $makeSignature) use ($sign, $sendWebhook, $makePayload): void {
    $link = CustomerAdvisorLink::factory()->parsingInProgress()->create();

    Queue::fake();

    $payload = $makePayload($link->parsing_job_id);

    $sendWebhook($payload, $makeSignature($payload, $sign))->assertUnauthorized();

    Queue::assertNothingPushed();
    assertDatabaseCount('inbound_webhooks', 0);
})->with([
    'signed for another tenant' => [fn (string $payload, Closure $sign): string => $sign($payload, (new Tenant())->forceFill(['id' => (string) Str::uuid()]))],
    'signed for a different body' => [fn (string $payload, Closure $sign): string => $sign("{$payload} ")],
    'not a signature' => [fn (string $payload, Closure $sign): string => 'sha256=not-a-signature'],
]);

it('rejects a webhook when there is no current tenant', function () use ($sign, $makePayload): void {
    $payload = $makePayload('pjb-123');
    $signature = $sign($payload);
    $url = route('inbound.webhook.llamaparse');

    Tenant::forgetCurrent();

    call('POST', $url, server: ['CONTENT_TYPE' => 'application/json', 'HTTP_LC_SIGNATURE' => $signature], content: $payload)
        ->assertUnauthorized();
});
