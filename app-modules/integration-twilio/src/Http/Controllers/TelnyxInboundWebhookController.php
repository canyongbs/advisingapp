<?php

namespace AdvisingApp\IntegrationTwilio\Http\Controllers;

use AdvisingApp\IntegrationTwilio\Jobs\ProcessTelnyxMessageReceived;
use AdvisingApp\IntegrationTwilio\Jobs\ProcessTelnyxMessageStatusUpdate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelnyxInboundWebhookController
{
    public function __invoke(Request $request): Response
    {
        $data = $request->toArray()['data'];

        throw_if(! isset($data['record_type']) || $data['record_type'] !== 'event', new Exception('Invalid Telnyx webhook record type'));

        if ($data['event_type'] === 'message.received') {
            dispatch(new ProcessTelnyxMessageReceived($data));

            return response()->noContent();
        }

        if ($data['event_type'] === 'message.sent' || $data['event_type'] === 'message.finalized') {
            dispatch(new ProcessTelnyxMessageStatusUpdate($data));

            return response()->noContent();
        }

        // Did not match any event_type we are currently parsing

        Log::warning('Telnyx event_type not matched', [
            'event_type' => $data['event_type'],
            'payload' => $request->getContent(),
        ]);

        return response()->make(status: 501);
    }
}
