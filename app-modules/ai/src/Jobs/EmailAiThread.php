<?php

namespace AdvisingApp\Ai\Jobs;

use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use AdvisingApp\Ai\Notifications\SendAssistantTranscriptNotification;

class EmailAiThread implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected AiThread $thread,
        protected User $sender,
        protected User $recipient,
    ) {}

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }

    public function handle(): void
    {
        $this->recipient->notify(new SendAssistantTranscriptNotification($this->thread, $this->sender));
    }
}
