<?php

use Twilio\Security\RequestValidator;

// it('will abort the request if the request does not have the necessary header', function () {
//     $response = $this->post(
//         route('inbound.webhook.twilio', 'status-update'),
//         // And the request does not have a valid secret
//         $this->loadFixtureFromModule('webhook', 'Twilio/StatusEvents/sent'),
//     );

//     $response->assertNotFound();
// });

// it('will abort the request if the request cannot be verified to have originated from Twilio', function () {
//     $response = $this->withHeaders([
//         'HTTP_X_TWILIO_SIGNATURE' => 'Not a legit signature',
//     ])->post(
//         route('inbound.webhook.twilio', 'status-update'),
//         $this->loadFixtureFromModule('webhook', 'Twilio/StatusEvents/sent'),
//     );

//     $response->assertNotFound();
// });

it('will allow the request if the signature is validated and the request is likely to have originated from Twilio', function () {
    // TODO As convenient as this might be, this is probably not something we actually want/need to test
    // We can probably trust that the Twilio validation will work, saving us from having to actual use
    // Any pieces of the client here. We want to mock it as much as we can, and don't want to have to actually
    // Interact with it during testing.
    $token = config('services.twilio.auth_token');
    $validator = new RequestValidator($token);

    $requestData = $this->loadFixtureFromModule('webhook', 'Twilio/StatusEvents/sent');

    $response = $this->withHeaders([
        'x-twilio-signature' => $validator->computeSignature(
            'http://localhost/inbound/webhook/twilio/status-update',
            $requestData,
        ),
    ])->post(
        route('inbound.webhook.twilio', 'status-update'),
        $requestData
    );

    $response->assertOk();
});
