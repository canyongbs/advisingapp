<?php

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Models\QnaAdvisor;
use Illuminate\Contracts\View\View;

class PreviewAdvisorEmbedController
{
    public function __invoke(QnaAdvisor $advisor): View
    {
        return view('ai::qna-advisor-preview', [
            'advisor' => $advisor,
        ]);
    }
}
