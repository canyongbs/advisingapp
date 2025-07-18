<?php

namespace AdvisingApp\Research\Events;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedSearchResults;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Arr;

class ResearchRequestSearchResultsParsed implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     *
     * @param array<string> $newSources
     */
    public function __construct(
        public ResearchRequest $researchRequest,
        public ResearchRequestParsedSearchResults $parsedSearchResults,
        public array $newSources = [],
    ) {}

    public function broadcastAs(): string
    {
        return 'research-request.search-results-parsed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'parsed_search_results' => Arr::except($this->parsedSearchResults->toArray(), ['results']),
            'new_sources' => $this->newSources,
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
