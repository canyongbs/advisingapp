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

namespace AdvisingApp\Ai\Jobs\CustomerAdvisors;

use AdvisingApp\Ai\Actions\GetCustomerAdvisorInstructions;
use AdvisingApp\Ai\Enums\AiReasoningEffort;
use AdvisingApp\Ai\Events\CustomerAdvisors\CustomerAdvisorMessageChunk;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorMessage;
use AdvisingApp\Ai\Models\CustomerAdvisorThread;
use AdvisingApp\Ai\Settings\AiCustomerAdvisorSettings;
use AdvisingApp\Ai\Support\StreamingChunks\Finish;
use AdvisingApp\Ai\Support\StreamingChunks\Meta;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Prism\Prism\Contracts\Message;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

class SendCustomerAdvisorMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    /**
     * @param array<string, mixed> $request
     */
    public function __construct(
        protected CustomerAdvisor $advisor,
        protected CustomerAdvisorThread $thread,
        protected string $content,
        protected array $request = [],
    ) {}

    public function handle(GetCustomerAdvisorInstructions $getCustomerAdvisorInstructions): void
    {
        $isStartOfConversation = ! $this->thread->messages()->where('is_advisor', false)->exists();

        $message = new CustomerAdvisorMessage();
        $message->thread()->associate($this->thread);
        $message->author()->associate($this->thread->author);
        $message->content = $this->content;
        $message->request = $this->request;
        $message->is_advisor = false;
        $message->save();

        try {
            $settings = app(AiCustomerAdvisorSettings::class);

            $effectiveModel = (! $settings->allow_selection_of_model && $settings->preselected_model)
                ? $settings->preselected_model
                : $this->advisor->model;

            $aiService = $effectiveModel->getService();

            $files = [
                ...$this->advisor->files()->whereNotNull('parsing_results')->get()->all(),
                ...$this->advisor->links()->whereNotNull('parsing_results')->get()->all(),
                ...$this->advisor->getResourceHubArticles(),
            ];

            $messages = $isStartOfConversation
                ? $this->thread->messages()
                    ->orderBy('created_at')
                    ->get()
                    ->map(fn (CustomerAdvisorMessage $message): Message => $message->is_advisor
                        ? new AssistantMessage($message->content)
                        : new UserMessage($message->content))
                    ->all()
                : [];

            $stream = $aiService->streamRaw(
                prompt: $context = $getCustomerAdvisorInstructions->execute($this->advisor),
                content: $this->content,
                files: $files,
                options: $this->thread->messages()->where('is_advisor', true)->latest()->value('next_request_options') ?? [],
                messages: $messages,
                filesContext: $this->advisor,
                reasoningEffort: AiReasoningEffort::Minimal,
            );

            $response = new CustomerAdvisorMessage();
            $response->thread()->associate($this->thread);
            $response->content = '';
            $response->context = $context;
            $response->is_advisor = true;

            $chunkBuffer = [];
            $chunkCount = 0;

            $finishChunk = null;

            foreach ($stream() as $chunk) {
                if ($chunk instanceof Meta) {
                    $response->message_id = $chunk->messageId;
                    $response->next_request_options = $chunk->nextRequestOptions;

                    continue;
                }

                if ($chunk instanceof Finish) {
                    $finishChunk = $chunk;

                    continue;
                }

                if ($chunk instanceof Text) {
                    $chunkBuffer[] = $chunk->content;
                    $chunkCount++;

                    if ($chunkCount >= 10) {
                        event(new CustomerAdvisorMessageChunk(
                            $this->advisor,
                            $this->thread,
                            content: implode('', $chunkBuffer),
                        ));
                        $response->content .= implode('', $chunkBuffer);

                        $chunkBuffer = [];
                        $chunkCount = 0;
                    }
                }
            }

            if ($finishChunk?->rateLimitResetsAt || $finishChunk?->error) {
                event(new CustomerAdvisorMessageChunk(
                    $this->advisor,
                    $this->thread,
                    content: '',
                    isComplete: false,
                    error: $finishChunk->error,
                    rateLimitResetsAt: $finishChunk->rateLimitResetsAt,
                ));

                return;
            }

            if ($finishChunk?->isIncomplete) {
                $chunkBuffer[] = '...';
                $chunkCount++;
            }

            if (! empty($chunkBuffer)) {
                event(new CustomerAdvisorMessageChunk(
                    $this->advisor,
                    $this->thread,
                    content: implode('', $chunkBuffer),
                ));
                $response->content .= implode('', $chunkBuffer);
            }

            event(new CustomerAdvisorMessageChunk(
                $this->advisor,
                $this->thread,
                content: '',
                isComplete: true,
            ));

            $response->save();
            $this->thread->touch();
        } catch (Throwable $exception) {
            report($exception);

            event(new CustomerAdvisorMessageChunk(
                $this->advisor,
                $this->thread,
                content: '',
                isComplete: false,
                error: 'An error happened when sending your message.',
            ));
        }
    }
}
