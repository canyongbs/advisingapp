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
use AdvisingApp\Research\Events\ResearchRequestResultsGenerated;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use App\Models\User;
use App\Support\ChunkIterator;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class GenerateResearchRequestSection implements ShouldQueue
{
    use Batchable;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    /**
     * @param array<string, mixed> $requestOptions
     */
    public function __construct(
        protected ResearchRequest $researchRequest,
        protected array $requestOptions = [],
    ) {}

    public function handle(): void
    {
        [
            'instructions' => $instructions,
            'remainingOutline' => $remainingOutline,
        ] = $this->getCurrentSection();

        $nextRequestOptions = null;

        try {
            $responseGenerator = $this->researchRequest->research_model
                ->getService()
                ->getResearchRequestRequestSection(
                    researchRequest: $this->researchRequest,
                    prompt: $this->getPrompt(),
                    content: $this->getContent($instructions),
                    options: $this->requestOptions,
                    nextRequestOptions: function (array $options) use (&$nextRequestOptions) {
                        $nextRequestOptions = $options;
                    },
                );

            if (filled($this->researchRequest->results)) {
                $this->researchRequest->results .= PHP_EOL;
                $this->researchRequest->results .= PHP_EOL;
            }

            $hasContent = false;

            foreach (app(ChunkIterator::class, ['iterator' => $responseGenerator, 'chunkSize' => 50])->get() as $responseContent) {
                $responseContent = implode($responseContent);

                $this->researchRequest->results .= $responseContent;
                $this->researchRequest->save();

                if ((! $hasContent) && filled($responseContent)) {
                    $hasContent = true;

                    $responseContent = PHP_EOL . PHP_EOL . $responseContent;
                }

                broadcast(app(ResearchRequestResultsGenerated::class, [
                    'researchRequest' => $this->researchRequest,
                    'resultsChunk' => $responseContent,
                ]));
            }
        } catch (MessageResponseException $exception) {
            if ($this->attempts() === 1) {
                report($exception); // Only report the exception on the first attempt to reduce noise in logs.
            }

            $this->release(delay: 10); // Allow time for the service to recover.

            return;
        }

        if (! $hasContent) {
            $this->release(delay: 10); // Allow time for the service to recover.

            return;
        }

        $this->researchRequest->remaining_outline = $remainingOutline;
        $this->researchRequest->save();

        if (blank($remainingOutline)) {
            $this->batch()->add(new FinishResearchRequest($this->researchRequest));

            return;
        }

        $this->batch()->add(new self($this->researchRequest, $nextRequestOptions));
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    /**
     * @return array{instructions: string, remainingOutline: array<string, mixed>}
     */
    protected function getCurrentSection(): array
    {
        $remainingOutline = $this->researchRequest->remaining_outline;

        if (array_key_exists('abstract', $remainingOutline)) {
            $instructions = "Generate the abstract section of the research report, with the H2 (##) heading \"{$remainingOutline['abstract']['heading']}\". The abstract should be 3 complete paragraphs.";

            unset($remainingOutline['abstract']);

            return [
                'instructions' => $instructions,
                'remainingOutline' => $remainingOutline,
            ];
        }

        if (array_key_exists('introduction', $remainingOutline)) {
            $instructions = "Generate the introduction section of the research report, with the H2 (##) heading \"{$remainingOutline['introduction']['heading']}\".";

            unset($remainingOutline['introduction']);

            return [
                'instructions' => $instructions,
                'remainingOutline' => $remainingOutline,
            ];
        }

        if (array_key_exists('sections', $remainingOutline)) {
            foreach ($remainingOutline['sections'] as $sectionIndex => $section) {
                if (array_key_exists('heading', $section)) {
                    $instructions = "Generate the section of the research report, with the H2 (##) heading \"{$section['heading']}\". Ensure that the content is cohesive and well-structured. The section should be 1 complete paragraph.";

                    unset($remainingOutline['sections'][$sectionIndex]['heading']);

                    return [
                        'instructions' => $instructions,
                        'remainingOutline' => $remainingOutline,
                    ];
                }

                if (array_key_exists('subsections', $section)) {
                    foreach ($section['subsections'] as $subsectionIndex => $subsection) {
                        $instructions = "Generate the subsection of the research report, with the H3 (###) heading \"{$subsection['heading']}\". Ensure that the content is cohesive and well-structured. The subsection should be 5 complete paragraphs.";

                        unset($remainingOutline['sections'][$sectionIndex]['subsections'][$subsectionIndex]);

                        if (blank($remainingOutline['sections'][$sectionIndex]['subsections'])) {
                            unset($remainingOutline['sections'][$sectionIndex]);
                        }

                        return [
                            'instructions' => $instructions,
                            'remainingOutline' => $remainingOutline,
                        ];
                    }

                    unset($remainingOutline['sections'][$sectionIndex]['subsections']);
                }

                unset($remainingOutline['sections'][$sectionIndex]);
            }

            unset($remainingOutline['sections']);
        }

        if (array_key_exists('conclusion', $remainingOutline)) {
            $instructions = "Generate the conclusion section of the research report, with the H2 (##) heading \"{$remainingOutline['conclusion']['heading']}\".";

            unset($remainingOutline['conclusion']);

            return [
                'instructions' => $instructions,
                'remainingOutline' => $remainingOutline,
            ];
        }

        $this->fail(new Exception('No remaining sections found for the research request.'));

        throw new Exception('No remaining sections found for the research request.');
    }

    protected function getContent(string $instructions): string
    {
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

            Ensure you follow the following additional rules.
            - All the content should be written as a scholar would at the PhD level.
            - Despite asking for the content one section at a time, ensure that the content is cohesive, and that you plan for the entirety of the content up front.
            - Avoid uses of any em dashes and use bullet lists sparingly.
            
            We are working through the outline for this research report. Currently, your task is to {$instructions}

            The content should be written in Markdown, where the heading (with the correct level, H2 (##) or H3 (###)) is the first line of the response, and the content is the rest of the paragraphs under the heading. Do not respond with any greetings or salutations, and do not include any additional information or context.
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
