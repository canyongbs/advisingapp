<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Bus\Queueable;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Multitenancy\Jobs\TenantAware;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService;

class ReInitializeAiThread implements ShouldQueue, TenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected AiThread $thread,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->thread->assistant->model->getService()->createThread($this->thread);
        $this->thread->save();

        auth()->setUser($this->thread->user);

        AiMessage::withoutEvents(function () {
            $service = $this->thread->assistant->model->getService();

            if (! ($service instanceof BaseOpenAiService)) {
                return;
            }

            $client = $service->getClient();

            $client->threads()->messages()->create($this->thread->thread_id, [
                'role' => 'user',
                'content' => 'Hello',
            ]);

            $client->threads()->runs()->create($this->thread->thread_id, [
                'assistant_id' => $this->thread->assistant->assistant_id,
                'instructions' => invade($client)->generateAssistantInstructions($this->thread->assistant, withDynamicContext: true),
            ]);
        });
    }
}
