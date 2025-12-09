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

use AdvisingApp\Ai\Actions\GetQnaAdvisorInstructions;
use AdvisingApp\Ai\Events\QnaAdvisors\QnaAdvisorMessageChunk;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
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

class SendQnaAdvisorMessage implements ShouldQueue
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
        protected QnaAdvisor $advisor,
        protected QnaAdvisorThread $thread,
        protected string $content,
        protected array $request = [],
    ) {}

    public function handle(GetQnaAdvisorInstructions $getQnaAdvisorInstructions): void
    {
        $isStartOfConversation = ! $this->thread->messages()->where('is_advisor', false)->exists();

        $message = new QnaAdvisorMessage();
        $message->thread()->associate($this->thread);
        $message->author()->associate($this->thread->author);
        $message->content = $this->content;
        $message->request = $this->request;
        $message->is_advisor = false;
        $message->save();

        try {
            $aiService = $this->advisor->model->getService();

            $messages = $isStartOfConversation
                ? $this->thread->messages()
                    ->orderBy('created_at')
                    ->get()
                    ->map(fn (QnaAdvisorMessage $message): Message => $message->is_advisor
                        ? new AssistantMessage($message->content)
                        : new UserMessage($message->content))
                    ->all()
                : [];

            $stream = $aiService->streamRaw(
                prompt: $context = $getQnaAdvisorInstructions->execute($this->advisor),
                content: $this->content,
                files: [
                    ...$this->advisor->files()->whereNotNull('parsing_results')->get()->all(),
                    ...$this->advisor->links()->whereNotNull('parsing_results')->get()->all(),
                ],
                options: $this->thread->messages()->where('is_advisor', true)->latest()->value('next_request_options') ?? [],
                messages: $messages,
            );

            $response = new QnaAdvisorMessage();
            $response->thread()->associate($this->thread);
            $response->content = '';
            $response->context = $context;
            $response->is_advisor = true;

            $chunkBuffer = [];
            $chunkCount = 0;

            foreach ($stream() as $chunk) {
                if ($chunk instanceof Meta) {
                    $response->message_id = $chunk->messageId;
                    $response->next_request_options = $chunk->nextRequestOptions;

                    continue;
                }

                if ($chunk instanceof Text) {
                    $chunkBuffer[] = $chunk->content;
                    $chunkCount++;

                    if ($chunkCount >= 30) {
                        event(new QnaAdvisorMessageChunk(
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

            if (! empty($chunkBuffer)) {
                event(new QnaAdvisorMessageChunk(
                    $this->advisor,
                    $this->thread,
                    content: implode('', $chunkBuffer),
                ));
                $response->content .= implode('', $chunkBuffer);
            }

            event(new QnaAdvisorMessageChunk(
                $this->advisor,
                $this->thread,
                content: '',
                isComplete: true,
            ));

            $response->save();
            $this->thread->touch();
        } catch (Throwable $exception) {
            report($exception);

            event(new QnaAdvisorMessageChunk(
                $this->advisor,
                $this->thread,
                content: '',
                isComplete: false,
                error: 'An error happened when sending your message.',
            ));
        }
    }
}
