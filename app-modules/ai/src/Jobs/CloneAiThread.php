<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Jobs;

use AdvisingApp\Ai\Models\AiThread;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

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
        try {
            DB::beginTransaction();

            $threadReplica = $this->thread->replicate(except: ['id', 'thread_id', 'folder_id', 'saved_at', 'emailed_count', 'cloned_count']);
            $threadReplica->saved_at = now();

            $threadReplica->user()->associate($this->recipient);
            $threadReplica->save();

            foreach ($this->thread->messages as $message) {
                $messageReplica = $message->replicate(['id', 'message_id']);
                $messageReplica->thread()->associate($threadReplica);
                $messageReplica->save();
            }

            $aiService = $threadReplica->assistant->model->getService();

            $threadReplica->locked_at = now();
            $threadReplica->save();

            $this->thread->cloned_count = $this->thread->cloned_count + 1;
            $this->thread->save();

            $aiService->ensureAssistantAndThreadExists($threadReplica);

            $threadReplica->locked_at = null;
            $threadReplica->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
