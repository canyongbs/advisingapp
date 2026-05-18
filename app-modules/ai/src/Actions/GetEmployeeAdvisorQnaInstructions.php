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

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Ai\Models\EmployeeAdvisorQuestion;

class GetEmployeeAdvisorQnaInstructions
{
    public function execute(AiAssistant $assistant): string
    {
        $assistant->loadMissing('categories.questions');

        $qnaSection = $this->generateQnaSection($assistant);

        if (blank($qnaSection)) {
            return $assistant->instructions ?? '';
        }

        $instructions = $assistant->instructions ?? '';

        return <<<END

        # Instructions
        {$instructions}

        {$qnaSection}
        END;
    }

    protected function generateQnaSection(AiAssistant $assistant): string
    {
        $categoriesWithQuestions = $assistant
            ->categories
            ->filter(fn (EmployeeAdvisorCategory $category) => $category->questions->isNotEmpty());

        if ($categoriesWithQuestions->isEmpty()) {
            return '';
        }

        $qnaSection = <<<END
        ## Questions and Answers
        This section contains the specialized knowledge you will use to answer questions via a conversational bot experience.


        END;

        $categoriesWithQuestions->each(function (EmployeeAdvisorCategory $category) use (&$qnaSection) {
            $questions = $category->questions->reduce(
                function (string $carry, EmployeeAdvisorQuestion $question) {
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
            The following questions are part of this category of knowledge for you to use when answering questions.

        {$questions}
        END;
        });

        return $qnaSection;
    }
}
