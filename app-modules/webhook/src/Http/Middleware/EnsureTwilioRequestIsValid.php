<?php

namespace Assist\Webhook\Http\Middleware;

use Closure;
use Twilio\Security\RequestValidator;

class EnsureTwilioRequestIsValid
{
    public function handle($request, Closure $next): mixed
    {
        ray('handle()');
        ray($request->all());
        $headers = collect($request->header())->transform(function ($item) {
            return $item[0];
        });

        ray('headers', $headers);

        // Your auth token from twilio.com/user/account
        $token = config('services.twilio.auth_token');

        // The X-Twilio-Signature header - in PHP this should be
        // You may be able to use $signature = $_SERVER["HTTP_X_TWILIO_SIGNATURE"];
        if (! $request->hasHeader('x-twilio-signature')) {
            ray('here...');
            abort(404);
        }

        ray('at least here...');

        $signature = $request->header('x-twilio-signature');

        // Initialize the request validator
        $validator = new RequestValidator($token);

        // It's my understanding that this is probably the target URL of the request,
        // Meaning our application and the specific endpoint we are hitting
        $url = 'https://example.com/myapp';

        // Check if the incoming signature is valid for your application URL and the incoming parameters
        if ($validator->validate($signature, $url, $request->all())) {
            ray('Confirmed to have come from Twilio.');
        } else {
            ray('NOT VALID. It might have been spoofed!');
        }

        // TODO We need to determine how to create this signature, as we can then test on our
        // End that all of our sample requests are actually working as expected. I think
        // Twilio gives a breakdown of exactly how this works.
    }
}
