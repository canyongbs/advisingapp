<?php

namespace Assist\Webhook\Http\Middleware;

use Closure;
use Aws\Sns\Message;
use Illuminate\Http\Request;
use Aws\Sns\MessageValidator;
use Symfony\Component\HttpFoundation\Response;

class VerifyAwsSnsRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasHeader('x-amz-sns-message-type')) {
            abort(404);
        }

        $message = Message::fromRawPostData();

        $validator = new MessageValidator();

        if (! $validator->isValid($message)) {
            abort(404);
        }

        return $next($request);
    }
}
