<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Jobs\Advisors;

use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use App\Features\AiThreadAutoNamingFeature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateAiThreadName implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        protected AiThread $thread,
    ) {}

    public function handle(): void
    {
        if (! AiThreadAutoNamingFeature::active()) {
            return;
        }

        // The thread may have been renamed by the user between when this job was
        // dispatched and when it runs, in which case it must not be overwritten.
        if (filled($this->thread->fresh()?->named_by_user_at)) {
            return;
        }

        $transcript = $this->thread->messages()
            ->oldest()
            ->get()
            ->map(fn (AiMessage $message): string => ($message->user_id ? 'User' : 'Assistant') . ": {$message->content}")
            ->implode(PHP_EOL . PHP_EOL);

        $prompt = $this->thread->assistant->instructions . "\nThe following is a chat between you and a user:\n" . $transcript;

        $aiService = $this->thread->assistant->model->getService();

        try {
            $name = $aiService->complete(
                $prompt,
                'Generate a title for this chat, in 5 words or less. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with the title:',
            );
        } catch (Throwable $exception) {
            report($exception);

            return;
        }

        if (filled($this->thread->fresh()?->named_by_user_at)) {
            return;
        }

        $this->thread->name = trim($name);
        $this->thread->saved_at = now();
        $this->thread->save();

        dispatch(new RecordTrackedEvent(
            type: TrackedEventType::AiThreadSaved,
            occurredAt: now(),
        ));
    }
}
