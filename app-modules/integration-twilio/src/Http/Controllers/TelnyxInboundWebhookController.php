<?php

namespace AdvisingApp\IntegrationTwilio\Http\Controllers;

use AdvisingApp\Engagement\Actions\CreateEngagementResponse;
use AdvisingApp\Engagement\DataTransferObjects\EngagementResponseData;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelnyxInboundWebhookController
{
    public function __invoke(Request $request): Response
    {
        // 1. Determine what kind of event this is
        $data = $request->toArray()['data'];

        throw_if(! isset($data['record_type']) || $data['record_type'] !== 'event', new Exception('Invalid Telnyx webhook record type'));

        if ($data['event_type'] === 'message.received') {
            return $this->processInboundMessageReceived($data);
        }

        if ($data['event_type'] === 'message.finalized') {
            return $this->processOutboundMessageStatusUpdate($data);
        }

        // 2. Execute the correct workload

        // 3. Respond back
    }

    protected function processInboundMessageReceived(array $data): Response
    {
        $createEngagementResponse = resolve(CreateEngagementResponse::class);

        $createEngagementResponse(EngagementResponseData::from([
            'from' => $data['payload']['from']['phone_number'],
            'body' => $data['payload']['text'],
        ]));

        return response()->noContent();
    }

    protected function processOutboundMessageStatusUpdate(array $data): Response
    {
        // Process the "message.status" event
    }
}
