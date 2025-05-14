<?php

namespace AdvisingApp\Research\Actions;

use App\Models\User;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Ai\Settings\AiResearchAssistantSettings;
use AdvisingApp\Research\Models\ResearchRequestQuestion;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;

class GenerateResearchQuestion
{
    public function execute(ResearchRequest $researchRequest): string
    {
        return app(AiIntegratedAssistantSettings::class)
            ->getDefaultModel()
            ->getService()
            ->complete(
                prompt: $this->getPrompt($researchRequest),
                content: $this->getContent($researchRequest),
            );
    }

    protected function getContent(ResearchRequest $researchRequest): string
    {
        $questions = $researchRequest->questions
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

            {$researchRequest->topic}

            ---{$questions}

            **Instructions:**
            
            Before the research is conducted, you need to help improve the changes that it will be relevant and useful to the me. Using my research topic, respond with one relevant question that will help to clarify it. You will be able to ask four questions in total, and you will know the answer to the previous question/s before you ask the next one. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with your question:
            EOD;
    }

    protected function getPrompt(ResearchRequest $researchRequest): string
    {
        /** @var User $user */
        $user = auth()->user();

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