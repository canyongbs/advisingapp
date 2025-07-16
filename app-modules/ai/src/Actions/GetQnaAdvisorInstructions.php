<?php

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use AdvisingApp\Ai\Models\QnaAdvisorQuestion;
use AdvisingApp\Ai\Settings\AiQnaAdvisorSettings;

class GetQnaAdvisorInstructions
{
    public function execute(QnaAdvisor $qnaAdvisor): string
    {
        $settings = app(AiQnaAdvisorSettings::class);

        $instructions = $settings->instructions ?? '';
        $backgroundInformation = $settings->background_information ?? '';

        $qnaSection = $this->generateQnaSection($qnaAdvisor);

        $restrictions = $settings->restrictions ?? '';

        return <<<END

        # Instructions
        {$instructions}

        ## Institutional Background Information
        {$backgroundInformation}

        {$qnaSection}
        ## Restrictions
        {$restrictions}

        END;
    }

    protected function generateQnaSection(QnaAdvisor $qnaAdvisor): string
    {
        $qnaAdvisor->loadMissing('categories.questions');

        /**
         * Note: Please be careful in adjusting tabing / spacing within the HEREDOCs as even a minor change can cause the markdown to render incorrectly.
         */
        $qnaSection = <<<END
        ## Questions and Answers
        This section contains the specialized knowledge you will use to answer questions from students via a conversational bot experience.


        END;

        $qnaAdvisor
            ->categories
            ->where(fn (QnaAdvisorCategory $category) => $category->questions->isNotEmpty())
            ->each(function (QnaAdvisorCategory $category) use (&$qnaSection) {
                $questions = $category->questions->reduce(
                    function (string $carry, QnaAdvisorQuestion $question) {
                        return $carry . <<<END
                        #### {$question->question}
                        {$question->answer}
                    
                    
                    END;
                    },
                    ''
                );

                $qnaSection .= <<<END
                ### Category
                {$category->name}
                {$category->description}
                The following questions are part of this category of knowledge for you to use when answering student questions.

            {$questions}
            END;
            });

        return $qnaSection;
    }
}
