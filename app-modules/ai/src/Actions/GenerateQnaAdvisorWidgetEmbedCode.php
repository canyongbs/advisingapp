<?php

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\QnaAdvisor;

class GenerateQnaAdvisorWidgetEmbedCode
{
    public function handle(QnaAdvisor $qnaAdvisor): string
    {
        $scriptUrl = url('qna-advisor-embed.js?');
        $formDefinitionUrl = "https://advising.app/qna-advisors/{$qnaAdvisor->getKey()}";

        return <<<EOD
        <qna-advisor url="{$formDefinitionUrl}"></qna-advisor>
        <script src="{$scriptUrl}"></script>
        EOD;
    }
}
