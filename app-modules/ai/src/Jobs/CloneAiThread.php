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

class CloneAiThread implements ShouldQueue
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
        $threadReplica = $this->thread->replicate(except: ['id', 'thread_id', 'folder_id']);
        $threadReplica->user()->associate($this->recipient);
        $threadReplica->assistant->model->getService()->createThread($threadReplica);
        $threadReplica->save();

        foreach ($this->thread->messages as $message) {
            $messageReplica = $message->replicate(['id', 'message_id']);
            $messageReplica->thread()->associate($threadReplica);
            $messageReplica->save();
        }
    }
}
