<?php

namespace AdvisingApp\Research\Jobs;

use Throwable;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use AdvisingApp\Ai\Settings\AiSettings;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Research\Actions\GenerateResearchTitle;
use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use GuzzleHttp\Client;

class Research implements ShouldQueue
{
    use Queueable;

    public $timeout = 600;

    public $tries = 3;

    public function __construct(
        protected ResearchRequest $researchRequest,
    ) {}

    public function handle(): void
    {
        try {
            $client = new Client();
            $response = $client->post('https://deepsearch.jina.ai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . app(AiIntegrationsSettings::class)->jina_deepsearch_ai_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'jina-deepsearch-v1',
                    'stream' => true,
                    'language_code' => 'en',
                    'messages' => [
                        ['role' => 'system', 'content' => $this->getPrompt()],
                        ['role' => 'user', 'content' => $this->getContent()],
                    ],
                    'temperature' => app(AiSettings::class)->temperature,
                ],
                'stream' => true,
            ]);

            $body = $response->getBody();

            $counter = 100;
            $stream = '';

            while (! $body->eof()) {
                $chunk = $body->read(1024);
                $stream .= $chunk;

                info($stream);

                $content = null;

                if (json_validate($stream)) {
                    $content = json_decode($stream, associative: true)['choices'][0]['delta']['content'] ?? null;
                    $stream = '';
                }

                $firstObjectInStream = '';

                if (str($stream)->contains('}{')) {
                    $firstObjectInStream = (string) str($stream)->before('}{') . '}';
                }

                if (json_validate($firstObjectInStream)) {
                    $content = json_decode($firstObjectInStream, associative: true)['choices'][0]['delta']['content'] ?? null;
                    $stream = '{' . ((string) str($stream)->after('}{'));
                }

                if (blank($content)) {
                    continue;
                }
                
                $counter++;

                $this->researchRequest->results .= $content;

                info($this->researchRequest->results);
                
                if ($counter > 100) {
                    $this->researchRequest->save();

                    $counter = 0;
                }
            }

            $this->researchRequest->save();

            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));

            if (blank($this->researchRequest->results)) {
                $this->researchRequest->update([
                    'results' => 'The artificial intelligence service was unavailable. Please try again later.',
                ]);

                return;
            }

            $this->researchRequest->update([
                'title' => app(AiIntegratedAssistantSettings::class)
                    ->getDefaultModel()
                    ->getService()
                    ->complete(
                        prompt: $this->researchRequest->results,
                        content: 'Generate a title for this research, in 5 words or less. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with the title:',
                    ),
            ]);
        } catch (Throwable $exception) {
            $this->researchRequest->update([
                'results' => 'The artificial intelligence service was unavailable. Please try again later.',
            ]);

            throw $exception;
        }
    }

    protected function getContent(): string
    {
        $questions = $this->researchRequest->questions
            ->map(fn (ResearchRequestQuestion $question, int $index) => '**Question ' . ($index + 1) . ":** {$question->content}" . PHP_EOL . '**Answer ' . ($index + 1) . ":** {$question->response}")
            ->implode(PHP_EOL . PHP_EOL);

        return <<<EOD
            **Research topic:**

            {$this->researchRequest->topic}

            ---
            
            **Clarification Q & A:**

            {$questions}
                
            ---

            **Instructions:**
            
            Using the context and clarifications above, conduct the requested research and present your findings accordingly. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with your research in Markdown format, without a title:
            EOD;
    }

    protected function getPrompt(): string
    {
        $user = $this->researchRequest->user;

        $userName = $user->name;
        $userJobTitle = filled($user->job_title) ? "with the job title **{$user->job_title}**" : '';

        $institutionalContext = app(AiResearchAssistantSettings::class)->context;

        return <<<EOD
            ## Requestor Information
            
            This request is submitted by **{$userName}** who is an institutional staff member {$userJobTitle}.

            ---

            ## Institutional Context

            {$institutionalContext}
            EOD;
    }
}
