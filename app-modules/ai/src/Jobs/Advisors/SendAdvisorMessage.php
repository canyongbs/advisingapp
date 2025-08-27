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

namespace AdvisingApp\Ai\Jobs\Advisors;

use AdvisingApp\Ai\Events\Advisors\AdvisorMessageChunk;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class SendAdvisorMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    /**
     * @param array<AiMessageFile> $files
     */
    public function __construct(
        protected AiThread $thread,
        protected string | Prompt $content,
        protected array $files = [],
    ) {}

    public function handle(): void
    {
        $message = new AiMessage();

        if ($this->content instanceof Prompt) {
            if ($this->content->is_smart) {
                $descriptionLine = $this->content->description
                    ? "with the description {$this->content->description}"
                    : null;

                $additionalContent = "Below I will provide you the input content for a prompt with the name {$this->content->title}, in the category {$this->content->type->title}" . ($descriptionLine ? ", {$descriptionLine}" : '') . '.
                The prompt may have variables {{ VARIABLE }} that are needed in order to effectively serve your function. Begin by analyzing the prompt.
                Begin by introducing yourself as an AI Advisor, and based on the prompt name, category, and description, explain what your purpose is. Then if the prompt has any variables in it, ask the user for that information, one variable at a time, explaining why you need that input from the user. Once all the variables are collected, return a response for the prompt supplied below.
                Note: If there are no variables, then just return a response for the prompt supplied below.';

                $message->content = $additionalContent . "\n\n" . $this->content->prompt;
            } else {
                $message->content = $this->content->prompt;
            }

            $use = $this->content->uses()->make();
            $use->user()->associate($this->thread->user);
            $use->save();
        } else {
            $message->content = $this->content;
        }

        $message->request = [
            'headers' => Arr::only(
                request()->headers->all(),
                ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
            ),
            'ip' => request()->ip(),
        ];
        $message->thread()->associate($this->thread);
        $message->user()->associate($this->thread->user);

        if ($this->content instanceof Prompt) {
            $message->prompt()->associate($this->content);
        }

        $message->save();

        $aiService = $this->thread->assistant->model->getService();

        $aiService->ensureAssistantAndThreadExists($this->thread);

        $response = new AiMessage();
        $response->thread()->associate($this->thread);

        Auth::setUser($this->thread->user);

        try {
            $stream = $aiService->sendMessage(
                message: $message,
                files: $this->files
            );
        } finally {
            // Reset the Auth user to avoid issues with subsequent jobs
            Auth::logout();
        }

        $chunkBuffer = [];
        $chunkCount = 0;

        foreach ($stream() as $chunk) {
            if ($chunk instanceof Text) {
                $chunkBuffer[] = $chunk->content;
                $chunkCount++;

                if ($chunkCount >= 30) {
                    event(new AdvisorMessageChunk(
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
            event(new AdvisorMessageChunk(
                $this->thread,
                content: implode('', $chunkBuffer),
            ));
            $response->content .= implode('', $chunkBuffer);
        }

        $response->save();
        $this->thread->touch();
    }
}
