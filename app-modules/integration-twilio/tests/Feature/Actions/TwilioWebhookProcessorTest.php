<?php

use function Pest\Laravel\post;

use Illuminate\Support\Facades\Queue;

use function Tests\loadFixtureFromModule;
use function Pest\Laravel\withoutMiddleware;

use Assist\IntegrationTwilio\Actions\StatusCallback;
use Assist\IntegrationTwilio\Actions\MessageReceived;

it('will dispatch an appropriate job to process the incoming request', function () {
    withoutMiddleware();

    Queue::fake();

    post(
        route('inbound.webhook.twilio', 'message_received'),
        loadFixtureFromModule('integration-twilio', 'MessageReceived/payload'),
    );

    Queue::assertPushed(MessageReceived::class);
    Queue::assertNotPushed(StatusCallback::class);
});
