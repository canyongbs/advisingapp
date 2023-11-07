<?php

namespace Assist\Webhook\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Assist\Webhook\Enums\InboundWebhookSource;
use Symfony\Component\HttpFoundation\Response;
use Assist\Webhook\Actions\StoreInboundWebhook;

class HandleAwsSnsRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = json_decode($request->getContent(), true);

        app(StoreInboundWebhook::class)
            ->handle(
                InboundWebhookSource::AwsSns,
                $data['Type'],
                $request->url(),
                json_encode($data)
            );

        if ($data['Type'] === 'SubscriptionConfirmation') {
            file_get_contents($data['SubscribeURL']);

            return response(status: 200);
        }

        if ($data['Type'] === 'UnsubscribeConfirmation') {
            // TODO: Look into whether or not we need to do something here, should we track this setup?
            return response(status: 200);
        }

        if ($data['Type'] !== 'Notification') {
            throw new Exception('Unknown AWS SNS webhook type');
        }

        return $next($request);
    }
}
