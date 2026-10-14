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

use AdvisingApp\IntegrationAwsSesEventHandling\Actions\DispatchSesEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindTenantFromData;
use AdvisingApp\IntegrationAwsSesEventHandling\Jobs\ConsumeSesEventsFromSqs;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use App\Models\Tenant;
use Aws\Result;
use Aws\Sns\MessageValidator;
use Aws\Sqs\SqsClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

use function Tests\loadFixtureFromModule;

/**
 * @param array<string, mixed> $tags
 */
$sesEventsQueueNotification = function (string $snsMessageId, array $tags): string {
    $envelope = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');
    $event = loadFixtureFromModule('integration-aws-ses-event-handling', 'Bounce');
    data_set($event, 'mail.tags', $tags);
    $envelope['MessageId'] = $snsMessageId;
    $envelope['Message'] = json_encode($event);

    return json_encode($envelope);
};

$sesEventsQueueSubscriptionConfirmation = function (string $subscribeUrl): string {
    $envelope = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');
    $envelope['Type'] = 'SubscriptionConfirmation';
    $envelope['Token'] = 'test-token';
    $envelope['SubscribeURL'] = $subscribeUrl;

    return json_encode($envelope);
};

$sesEventsQueueClient = function (string $body): MockInterface {
    $client = Mockery::mock(SqsClient::class);
    $client->shouldReceive('receiveMessage')->andReturn(
        new Result(['Messages' => [['MessageId' => 'sqs-message', 'ReceiptHandle' => 'receipt-handle', 'Body' => $body]]]),
        new Result([]),
    );

    return $client;
};

$consumeSesEvents = function (MockInterface $client, bool $isValidSignature = true): void {
    $validator = Mockery::mock(MessageValidator::class);
    $validator->shouldReceive('isValid')->andReturn($isValidSignature);
    assert($client instanceof SqsClient);
    assert($validator instanceof MessageValidator);
    app(ConsumeSesEventsFromSqs::class)->handle(app(DispatchSesEvent::class), $validator, $client);
};

beforeEach(function () {
    config(['services.ses_events_queue.url' => 'https://sqs.us-west-2.amazonaws.com/000000000000/ses-events']);
});

it('dispatches the SES events in the queue to their tenant and deletes them', function () use ($sesEventsQueueClient, $sesEventsQueueNotification, $consumeSesEvents) {
    Event::fake([SesBounceEvent::class]);

    $tenant = Tenant::query()->firstOrFail();

    $client = $sesEventsQueueClient($sesEventsQueueNotification('sns-message', ['tenant_id' => [$tenant->getKey()]]));
    $client->shouldReceive('deleteMessageBatch')
        ->once()
        ->withArgs(fn (array $arguments): bool => $arguments['Entries'] === [['Id' => 'sqs-message', 'ReceiptHandle' => 'receipt-handle']]);

    $consumeSesEvents($client);

    Event::assertDispatched(SesBounceEvent::class, fn (SesBounceEvent $event): bool => $event->snsMessageId === 'sns-message');
});

it('deletes an SES event that was already recorded without dispatching it again', function () use ($sesEventsQueueClient, $sesEventsQueueNotification, $consumeSesEvents) {
    Event::fake([SesBounceEvent::class]);

    $tenant = Tenant::query()->firstOrFail();

    $emailMessage = $tenant->execute(function () {
        $emailMessage = EmailMessage::factory()->create();

        $emailMessage->events()->create([
            'type' => EmailMessageEventType::Bounce,
            'payload' => [],
            'occurred_at' => now(),
            'sns_message_id' => 'sns-message',
        ]);

        return $emailMessage;
    });

    $client = $sesEventsQueueClient($sesEventsQueueNotification('sns-message', [
        'app_message_id' => [$emailMessage->getKey()],
        'tenant_id' => [$tenant->getKey()],
    ]));
    $client->shouldReceive('deleteMessageBatch')->once();

    $consumeSesEvents($client);

    Event::assertNotDispatched(SesBounceEvent::class);
});

it('reports and deletes an SES event that cannot be routed to a tenant', function (array $tags) use ($sesEventsQueueClient, $sesEventsQueueNotification, $consumeSesEvents) {
    Exceptions::fake();
    Event::fake([SesBounceEvent::class]);

    $client = $sesEventsQueueClient($sesEventsQueueNotification('sns-message', $tags));
    $client->shouldReceive('deleteMessageBatch')->once();

    $consumeSesEvents($client);

    Exceptions::assertReported(CouldNotFindTenantFromData::class);
    Event::assertNotDispatched(SesBounceEvent::class);
})->with([
    'without a tenant tag' => [[]],
    'for a tenant that does not exist' => [['tenant_id' => ['0199b5a4-0000-7000-8000-000000000000']]],
]);

it('leaves a message for redelivery when it cannot be handled', function (string $body, bool $isValidSignature) use ($sesEventsQueueClient, $consumeSesEvents) {
    Exceptions::fake();
    Event::fake([SesBounceEvent::class]);

    $client = $sesEventsQueueClient($body);
    $client->shouldReceive('deleteMessageBatch')->never();

    $consumeSesEvents($client, $isValidSignature);

    Event::assertNotDispatched(SesBounceEvent::class);
})->with([
    'with an invalid signature' => fn () => [$sesEventsQueueNotification('sns-message', ['tenant_id' => [Tenant::query()->firstOrFail()->getKey()]]), false],
    'with a body that is not JSON' => ['not-json', true],
]);

it('confirms a subscription before deleting its message', function (int $status, int $deletes) use ($sesEventsQueueClient, $sesEventsQueueSubscriptionConfirmation, $consumeSesEvents) {
    Http::fake(['*' => Http::response(status: $status)]);

    $client = $sesEventsQueueClient($sesEventsQueueSubscriptionConfirmation('https://sns.us-west-2.amazonaws.com/confirm'));
    $client->shouldReceive('deleteMessageBatch')->times($deletes);

    $consumeSesEvents($client);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://sns.us-west-2.amazonaws.com/confirm');
})->with([
    'confirmed' => [200, 1],
    'not confirmed' => [500, 0],
]);

it('does nothing when the queue is not configured', function () use ($consumeSesEvents) {
    config(['services.ses_events_queue.url' => null]);

    $client = Mockery::mock(SqsClient::class);
    $client->shouldReceive('receiveMessage')->never();

    $consumeSesEvents($client);
});
