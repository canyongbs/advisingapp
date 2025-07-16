<?php

namespace AdvisingApp\Ai\Observers;

use AdvisingApp\Ai\Models\QnaAdvisorQuestion;
use Illuminate\Support\Facades\Cache;

class QnaAdvisorQuestionObserver
{
    public function saved(QnaAdvisorQuestion $question): void
    {
        Cache::tags(['{qna_advisor_instructions}'])->forget($question->category->qnaAdvisor->getInstructionsCacheKey());
    }

    public function deleted(QnaAdvisorQuestion $question): void
    {
        Cache::tags(['{qna_advisor_instructions}'])->forget($question->category->qnaAdvisor->getInstructionsCacheKey());
    }
}
