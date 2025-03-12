<?php

namespace AdvisingApp\IntegrationTwilio\Http\Middleware;

use AdvisingApp\Webhook\Actions\StoreInboundWebhook;
use AdvisingApp\Webhook\Enums\InboundWebhookSource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogTwilioRequest
{
    public function __construct(
        protected StoreInboundWebhook $storeInboundWebhook
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->storeInboundWebhook->handle(
            source: InboundWebhookSource::Twilio,
            event: $request->route('event'),
            url: $request->url(),
            payload: is_array($request->getContent()) ? json_encode($request->getContent()) : $request->getContent()
        );

        return $next($request);
    }
}
