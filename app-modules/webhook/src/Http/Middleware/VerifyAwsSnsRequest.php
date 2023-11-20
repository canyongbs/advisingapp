<?php

namespace Assist\Webhook\Http\Middleware;

use Closure;
use Exception;
use Aws\Sns\Message;
use Illuminate\Http\Request;
use Aws\Sns\MessageValidator;
use Symfony\Component\HttpFoundation\Response;

class VerifyAwsSnsRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (! $request->hasHeader('x-amz-sns-message-type')) {
                throw new Exception('SNS message type header not provided.', 404);
            }

            $message = new Message(json_decode($request->getContent(), true));

            if (! app(MessageValidator::class)->isValid($message)) {
                throw new Exception('SNS message validation failed.', 404);
            }

            return $next($request);
        } catch (Exception $exception) {
            report($exception);
            abort(array_key_exists($exception->getCode(), Response::$statusTexts) ? $exception->getCode() : 500);
        }
    }
}
