<?php

namespace AdvisingApp\Ai\Jobs\QnaAdvisors;

use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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

        assert($this->thread->author instanceof Student || $this->thread->author instanceof Prospect);

        $this->thread->author->interactions()->create([
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
    }
}
