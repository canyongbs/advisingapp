<?php

namespace AdvisingApp\Ai\Observers;

use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use Illuminate\Support\Facades\Cache;

class QnaAdvisorCategoryObserver
{
    public function saved(QnaAdvisorCategory $category): void
    {
        Cache::forget($category->qnaAdvisor->getInstructionsCacheKey());
    }

    public function deleted(QnaAdvisorCategory $category): void
    {
        Cache::forget($category->qnaAdvisor->getInstructionsCacheKey());
    }
}
