<?php

it('will abort the request if the request does not have the necessary header', function () {
    $response = $this->post(
        route('inbound.webhook.twilio', 'status_update'),
        $this->loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    $response->assertNotFound();
});

it('will abort the request if the request cannot be verified to have originated from Twilio', function () {
    $response = $this->withHeaders([
        'x-twilio-signature' => 'Not a legit signature',
    ])->post(
        route('inbound.webhook.twilio', 'status_update'),
        $this->loadFixtureFromModule('integration-twilio', 'StatusCallback/sent'),
    );

    $response->assertNotFound();
});
