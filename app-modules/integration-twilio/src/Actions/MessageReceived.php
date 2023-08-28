<?php

namespace Assist\IntegrationTwilio\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Engagement\Actions\CreateEngagementResponse;

class MessageReceived implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function handle(CreateEngagementResponse $createEngagementResponse): void
    {
        // TODO We might want to normalize data before trying to create an EngagementResponse
        // This way we can do this for every integration in the future. For now, it's probably
        // Overkill, but definitely something to think about a bit in the future.
        $createEngagementResponse($this->data);
    }
}
