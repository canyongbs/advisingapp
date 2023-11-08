<?php

namespace Assist\Webhook\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Assist\Webhook\Enums\InboundWebhookSource;
use Symfony\Component\HttpFoundation\Response;
use Assist\Webhook\Actions\StoreInboundWebhook;
use Assist\Webhook\DataTransferObjects\SnsMessage;

class HandleAwsSnsRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = SnsMessage::fromRequest($request);

        app(StoreInboundWebhook::class)
            ->handle(
                InboundWebhookSource::AwsSns,
                in_array($data->type, ['SubscriptionConfirmation', 'UnsubscribeConfirmation', 'Notification']) ? $data->type : 'UnknownSnsType',
                $request->url(),
                $request->getContent()
            );

        if ($data->type === 'SubscriptionConfirmation') {
            file_get_contents($data->subscribeURL);

            return response(status: 200);
        }

        if ($data->type === 'UnsubscribeConfirmation') {
            return response(status: 200);
        }

        if ($data->type !== 'Notification') {
            throw new Exception('Unknown AWS SNS webhook type');
        }

        return $next($request);
    }
}
