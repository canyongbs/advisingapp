<?php

namespace AdvisingApp\Ai\Events\QnaAdvisors;

use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndQnaAdvisorThread implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public QnaAdvisorThread $thread,) {}

    public function broadcastAs(): string
    {
        return 'qna-advisor.automatic-end';
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $channelName = "qna-advisor-thread-{$this->thread->getKey()}";

        return [
            $this->thread->advisor->is_requires_authentication_enabled
                ? new PrivateChannel($channelName)
                : new Channel($channelName),
        ];
    }
}
