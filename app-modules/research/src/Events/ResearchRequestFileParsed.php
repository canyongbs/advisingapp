<?php

namespace AdvisingApp\Research\Events;

use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedFile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Arr;

class ResearchRequestFileParsed implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public ResearchRequest $researchRequest,
        public ResearchRequestParsedFile $parsedFile,
    ) {}

    public function broadcastAs(): string
    {
        return 'research-request.file-parsed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->parsedFile->loadMissing(['media']);
        $this->parsedFile->media->temporary_url = $this->parsedFile->media->getTemporaryUrl(now()->addDay()); /** @phpstan-ignore property.notFound */

        return [
            'parsed_file' => Arr::except($this->parsedFile->toArray(), ['results']),
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
