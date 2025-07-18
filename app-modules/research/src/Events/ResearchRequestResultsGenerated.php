<?php

namespace AdvisingApp\Research\Events;

use AdvisingApp\Research\Models\ResearchRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ResearchRequestResultsGenerated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public ResearchRequest $researchRequest,
        public string $resultsChunk,
    ) {}

    public function broadcastAs(): string
    {
        return 'research-request.results-generated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'results_chunk' => $this->resultsChunk,
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
