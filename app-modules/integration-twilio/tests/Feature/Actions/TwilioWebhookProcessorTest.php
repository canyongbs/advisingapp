<?php

use Illuminate\Support\Facades\Queue;
use Assist\IntegrationTwilio\Actions\StatusUpdate;
use Assist\IntegrationTwilio\Actions\MessageReceived;

it('will dispatch an appropriate job to process the incoming request', function () {
    $this->withoutMiddleware();

    Queue::fake();

    $this->post(
        route('inbound.webhook.twilio', 'message_received'),
        // TODO Change this payload for this specific test...
        $this->loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    Queue::assertPushed(MessageReceived::class);
    Queue::assertNotPushed(StatusUpdate::class);
});
