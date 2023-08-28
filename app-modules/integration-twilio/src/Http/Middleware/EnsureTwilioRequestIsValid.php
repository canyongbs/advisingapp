<?php

namespace Assist\IntegrationTwilio\Http\Middleware;

use Closure;
use Twilio\Security\RequestValidator;

class EnsureTwilioRequestIsValid
{
    public function handle($request, Closure $next): mixed
    {
        if (! $request->hasHeader('x-twilio-signature')) {
            abort(404);
        }

        $validator = new RequestValidator(config('services.twilio.auth_token'));

        if (! $validator->validate($request->header('x-twilio-signature'), $request->url, $request->all())) {
            abort(404);
        }

        return $next($request);
    }
}
