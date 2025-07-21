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

use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use AdvisingApp\Research\Events\ResearchRequestSearchQueriesGenerated;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use App\Models\User;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class GenerateResearchRequestSearchQueries implements ShouldQueue
{
    use Batchable;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public function __construct(
        protected ResearchRequest $researchRequest,
    ) {}

    public function handle(): void
    {
        $settings = app(AiResearchAssistantSettings::class);

        if (! $settings->research_model) {
            $this->fail(new Exception('Research model is not set in the settings.'));

            return;
        }

        try {
            $searchQueries = $settings->research_model
                ->getService()
                ->getResearchRequestRequestSearchQueries(
                    researchRequest: $this->researchRequest,
                    prompt: $this->getPrompt(),
                    content: $this->getContent(),
                );
        } catch (MessageResponseException $exception) {
            if ($this->attempts() === 1) {
                report($exception); // Only report the exception on the first attempt to reduce noise in logs.
            }

            $this->release(delay: 10); // Allow time for the service to recover.

            return;
        }

        if (blank($searchQueries)) {
            $this->release(delay: 10); // Allow time for the service to recover.

            return;
        }

        $numberOfSearchQueries = app(AiResearchAssistantSettings::class)->reasoning_effort->getNumberOfSearchQueries();

        if (count($searchQueries) < $numberOfSearchQueries) {
            $this->release();

            return;
        }

        $searchQueries = Arr::take($searchQueries, $numberOfSearchQueries);

        $this->researchRequest->search_queries = $searchQueries;
        $this->researchRequest->save();

        $this->batch()->add(array_map(
            fn (string $searchQuery): FetchResearchRequestSearchQueryParsingResults => app(FetchResearchRequestSearchQueryParsingResults::class, [
                'researchRequest' => $this->researchRequest,
                'searchQuery' => $searchQuery,
            ]),
            $searchQueries,
        ));

        broadcast(app(ResearchRequestSearchQueriesGenerated::class, [
            'researchRequest' => $this->researchRequest,
        ]));
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    protected function getContent(): string
    {
        $numberOfSearchQueries = app(AiResearchAssistantSettings::class)->reasoning_effort->getNumberOfSearchQueries();

        $questions = $this->researchRequest->questions
            ->map(fn (ResearchRequestQuestion $question, int $index) => '**Question ' . ($index + 1) . ":** {$question->content}" . PHP_EOL . '**Answer ' . ($index + 1) . ":** {$question->response}")
            ->implode(PHP_EOL . PHP_EOL);

        if (filled($questions)) {
            $questions = <<<EOD

            
                **Clarification Q & A:**

                {$questions}
                
                ---
                EOD;
        }

        return <<<EOD
            **Research topic:**

            {$this->researchRequest->topic}

            ---{$questions}

            **Instructions:**

            Use the file content of the attached vector store as an input to your analysis.
            
            Before the research is conducted, you need to help improve the chances that it will be relevant and useful to me. Using my research topic and Q&A, generate {$numberOfSearchQueries} web search phrase/s that might be used in Google that will provider web page targets that would be helpful for discovering the answer to this question:
            EOD;
    }

    protected function getPrompt(): string
    {
        /** @var User $user */
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
