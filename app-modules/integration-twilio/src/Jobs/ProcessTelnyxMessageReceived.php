<?php

namespace AdvisingApp\IntegrationTwilio\Jobs;

use AdvisingApp\Engagement\Actions\CreateEngagementResponse;
use AdvisingApp\Engagement\DataTransferObjects\EngagementResponseData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTelnyxMessageReceived implements ShouldQueue
{
    use Queueable;

    /**
     * @param array{payload: array{from: array{phone_number: string}, text: string}} $data
     */
    public function __construct(
        protected array $data
    ) {}

    public function handle(): void
    {
        $createEngagementResponse = resolve(CreateEngagementResponse::class);

        $createEngagementResponse(EngagementResponseData::from([
            'from' => $this->data['payload']['from']['phone_number'],
            'body' => $this->data['payload']['text'],
        ]));
    }
}
