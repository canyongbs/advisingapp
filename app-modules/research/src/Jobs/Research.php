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

namespace AdvisingApp\Research\Jobs;

use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class Research implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public int $tries = 3;

    public function __construct(
        protected ResearchRequest $researchRequest,
    ) {}

    public function handle(): void
    {
        try {
            $client = new Client();
            $response = $client->post('https://deepsearch.jina.ai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . app(AiIntegrationsSettings::class)->jina_deepsearch_v1_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'jina-deepsearch-v1',
                    'stream' => true,
                    'language_code' => 'en',
                    'messages' => [
                        ['role' => 'user', 'content' => $this->getContent()],
                    ],
                    'temperature' => app(AiSettings::class)->temperature,
                    'reasoning_effort' => app(AiResearchAssistantSettings::class)->reasoning_effort,
                ],
                'stream' => true,
                'no_direct_answer' => true,
            ]);

            $body = $response->getBody();

            $counter = 0;
            $buffer = '';
            $eventJoinPattern = "/\r\n\r\n|\n\n|\r\r/";

            while (! $body->eof()) {
                $buffer .= $body->read(1024);

                while (preg_match($eventJoinPattern, $buffer)) {
                    $parts = preg_split($eventJoinPattern, $buffer, 2);

                    $rawEvent = $parts[0] ?? '';
                    $remaining = $parts[1] ?? '';

                    $buffer = $remaining;

                    $matched = preg_match('/(?P<name>[^:]*):?( ?(?P<value>.*))?/', $rawEvent, $matches);

                    if (! $matched) {
                        continue;
                    }

                    if (
                        blank($matches['name']) ||
                        blank($matches['value'])
                    ) {
                        continue;
                    }

                    if ($matches['name'] !== 'data') {
                        continue;
                    }

                    if (! json_validate($matches['value'])) {
                        continue;
                    }

                    $data = json_decode($matches['value'], associative: true);

                    $content = $data['choices'][0]['delta']['content'] ?? null;

                    $this->researchRequest->results .= $content;

                    if (blank($content)) {
                        continue;
                    }

                    $counter++;

                    if ($counter >= 20) {
                        $this->researchRequest->save();

                        $counter = 0;
                    }
                }
            }

            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));

            if (blank($this->researchRequest->results)) {
                $this->researchRequest->results = 'The artificial intelligence service was unavailable. Please try again later.';
                $this->researchRequest->touch('finished_at');

                return;
            }

            try {
                $this->researchRequest->title = app(AiIntegratedAssistantSettings::class)
                    ->getDefaultModel()
                    ->getService()
                    ->complete(
                        prompt: $this->researchRequest->results,
                        content: 'Generate a title for this research, in 5 words or less. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with the title:',
                    );
            } catch (Throwable $exception) {
                report($exception);

                $this->researchRequest->title = 'Untitled Research';
            }

            $this->researchRequest->touch('finished_at');
        } catch (Throwable $exception) {
            $this->researchRequest->results = 'The artificial intelligence service was unavailable. Please try again later.';
            $this->researchRequest->touch('finished_at');

            report($exception);
        }
    }

    protected function getContent(): string
    {
        $user = $this->researchRequest->user;

        $userName = $user->name;
        $userJobTitle = filled($user->job_title) ? "with the job title **{$user->job_title}**" : '';

        $institutionalContext = app(AiResearchAssistantSettings::class)->context;

        $questions = $this->researchRequest->questions
            ->map(fn (ResearchRequestQuestion $question, int $index) => '**Question ' . ($index + 1) . ":** {$question->content}" . PHP_EOL . '**Answer ' . ($index + 1) . ":** {$question->response}")
            ->implode(PHP_EOL . PHP_EOL);

        return <<<EOD
            **Requestor Information**
            
            This request is submitted by **{$userName}** who is an institutional staff member {$userJobTitle}.

            ---

            **Institutional Context**

            {$institutionalContext}

            ---

            **Research topic:**

            {$this->researchRequest->topic}

            ---
            
            **Clarification Q & A:**

            {$questions}
                
            ---

            **Instructions:**
            
            Using the context and clarifications above, conduct the requested research and present your findings accordingly. Do not respond with any greetings or salutations, and do not include any additional information or context. Cite your sources and provide footnotes with references in the research. Prioritize sources that are based on peer reviewed journals, articles presented on official government or education domains, or websites that you determine to have a very high credibility rating like the Associated Press (AP), Reuters, Agence France-Presse (AFP) and BBC News, etc. When the research requires data anlsysis, always prioritize official government sources of data, or data directly presented by institutions via official websites with .edu or .gov domain names. For example, in the US, NCES and IPEDS are the best source of enrollment information of colleges. Direct college reporting on their institutional website (e.g., harvard.edu), would be second to that. Just respond with your research in Markdown format, without a title:
            EOD;
    }
}
