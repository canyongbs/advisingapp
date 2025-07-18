<?php

namespace AdvisingApp\Research\Events;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedLink;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Arr;

class ResearchRequestLinkParsed implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public ResearchRequest $researchRequest,
        public ResearchRequestParsedLink $parsedLink,
    ) {}

    public function broadcastAs(): string
    {
        return 'research-request.link-parsed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'parsed_link' => Arr::except($this->parsedLink->toArray(), ['results']),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("research-request-{$this->researchRequest->getKey()}"),
        ];
    }
}
