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

namespace AdvisingApp\Ai\Jobs\QnaAdvisors;

use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class CreateQnaAdvisorThreadInteraction implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        protected QnaAdvisorThread $thread,
    ) {}

    public function handle(): void
    {
        $transcript = $this->thread->messages()
            ->oldest()
            ->get()
            ->map(fn (QnaAdvisorMessage $message): string => ($message->is_advisor ? "**{$this->thread->advisor->name}:** " : "**{$message->author->getAttribute('full_name')}:** ") . $message->content)
            ->implode(PHP_EOL . PHP_EOL);

        $model = app(AiIntegratedAssistantSettings::class)->getDefaultModel();

        $summary = app(CompletePrompt::class)->execute(
            aiModel: $model,
            prompt: <<<EOL
                Please provide a concise summary of the following conversation between an advisor chatbot and a user.
                The summary should capture the main topics discussed and any important details.
                Format the summary in complete sentences.
                You should only respond with the summary, you should never greet the user.
            EOL,
            content: $transcript,
        );

        DB::transaction(function () use ($summary, $transcript) {
            assert($this->thread->author instanceof Student || $this->thread->author instanceof Prospect);

            $interaction = $this->thread->author->interactions()->create([
                'start_datetime' => $this->thread->messages()->oldest()->value('created_at'),
                'end_datetime' => $this->thread->messages()->latest()->value('created_at'),
                'subject' => "{$this->thread->advisor->name} (QnA Advisor) Chat Session",
                'description' => <<<"EOD"
                    **Summary:**

                    {$summary}

                    **Transcript:**

                    {$transcript}
                    EOD,
            ]);

            $this->thread->interaction()->associate($interaction);
            $this->thread->save();
        });
    }
}
