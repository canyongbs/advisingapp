<?php

namespace Assist\IntegrationTwilio\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Engagement\Actions\CreateEngagementResponse;
use Assist\Engagement\DataTransferObjects\EngagementResponseData;

class MessageReceived implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function handle(): void
    {
        $createEngagementResponse = resolve(CreateEngagementResponse::class);

        $createEngagementResponse(EngagementResponseData::from([
            'from' => $this->data['From'],
            'body' => $this->data['Body'],
        ]));
    }
}
