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

use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use App\Models\User;
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

    public int $tries = 3;

    /**
     * @param array<string, mixed> $remainingSections
     * @param array<string, mixed> $requestOptions
     */
    public function __construct(
        protected ResearchRequest $researchRequest,
        protected array $remainingSections = [],
        protected array $requestOptions = [],
    ) {}

    public function handle(): void
    {
        $settings = app(AiResearchAssistantSettings::class);

        throw_if(
            ! $settings->research_model,
            new Exception('Research model is not set in the settings.')
        );

        [
            'instructions' => $instructions,
            'remainingSections' => $remainingSections,
        ] = $this->getCurrentSection();

        $nextRequestOptions = null;

        $responseGenerator = $settings->research_model
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

        foreach ($responseGenerator as $responseContent) {
            $this->researchRequest->results .= $responseContent;
        }

        $this->researchRequest->save();

        if (blank($remainingSections)) {
            return;
        }

        $this->batch()->add(app(static::class, [
            'researchRequest' => $this->researchRequest,
            'remainingSections' => $remainingSections,
            'requestOptions' => $nextRequestOptions,
        ]));
    }

    /**
     * @return array{instructions: string, remainingSections: array<string, mixed>}
     */
    protected function getCurrentSection(): array
    {
        if (array_key_exists('abstract', $this->remainingSections)) {
            $instructions = "Generate the abstract section of the research report, with the H2 heading \"{$this->remainingSections['abstract']['heading']}\". The abstract should be 3 complete paragraphs.";

            $remainingSections = $this->remainingSections;
            unset($remainingSections['abstract']);

            return [
                'instructions' => $instructions,
                'remainingSections' => $remainingSections,
            ];
        }

        if (array_key_exists('introduction', $this->remainingSections)) {
            $instructions = "Generate the introduction section of the research report, with the H2 heading \"{$this->remainingSections['introduction']['heading']}\".";

            $remainingSections = $this->remainingSections;
            unset($remainingSections['introduction']);

            return [
                'instructions' => $instructions,
                'remainingSections' => $remainingSections,
            ];
        }

        if (array_key_exists('sections', $this->remainingSections)) {
            foreach ($this->remainingSections['sections'] as $sectionIndex => $section) {
                if (array_key_exists('heading', $section)) {
                    $instructions = "Generate the section of the research report, with the H2 heading \"{$section['heading']}\". Ensure that the content is cohesive and well-structured. The section should be 5 complete paragraphs.";

                    $remainingSections = $this->remainingSections;
                    unset($remainingSections['sections'][$sectionIndex]['heading']);

                    return [
                        'instructions' => $instructions,
                        'remainingSections' => $remainingSections,
                    ];
                }

                if (array_key_exists('subsections', $section)) {
                    foreach ($section['subsections'] as $subsectionIndex => $subsection) {
                        $instructions = "Generate the subsection of the research report, with the H3 heading \"{$subsection['heading']}\". Ensure that the content is cohesive and well-structured. The subsection should be 5 complete paragraphs.";

                        $remainingSections = $this->remainingSections;
                        unset($remainingSections['sections'][$sectionIndex]['subsections'][$subsectionIndex]);

                        if (blank($remainingSections['sections'][$sectionIndex]['subsections'])) {
                            unset($remainingSections['sections'][$sectionIndex]);
                        }

                        return [
                            'instructions' => $instructions,
                            'remainingSections' => $remainingSections,
                        ];
                    }

                    $remainingSections = $this->remainingSections;
                    unset($remainingSections['sections'][$sectionIndex]['subsections']);
                }

                $remainingSections = $this->remainingSections;
                unset($remainingSections['sections'][$sectionIndex]);
            }

            $remainingSections = $this->remainingSections;
            unset($remainingSections['sections']);
        }

        if (array_key_exists('conclusion', $this->remainingSections)) {
            $instructions = "Generate the conclusion section of the research report, with the H2 heading \"{$this->remainingSections['conclusion']['heading']}\".";

            $remainingSections = $this->remainingSections;
            unset($remainingSections['conclusion']);

            return [
                'instructions' => $instructions,
                'remainingSections' => $this->remainingSections,
            ];
        }

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

            The content should be written in Markdown, where the heading is the first line of the response, and the content is the rest of the paragraphs under the heading. Do not respond with any greetings or salutations, and do not include any additional information or context.
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
