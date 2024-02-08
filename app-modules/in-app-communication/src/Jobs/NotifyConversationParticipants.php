<?php

namespace AdvisingApp\InAppCommunication\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\InAppCommunication\Events\ConversationMessageSent;

class NotifyConversationParticipants implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        readonly public ConversationMessageSent $event,
    ) {}

    public function handle(): void
    {
        $this->event->conversation->participants()
            ->whereKeyNot($this->event->author)
            ->lazyById(100)
            ->each(function (User $participant) {
                dispatch(new NotifyConversationParticipant($this->event, $participant));
            });
    }
}
