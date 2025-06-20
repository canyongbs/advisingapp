<?php

namespace AdvisingApp\Ai\Actions\Widgets;

use AdvisingApp\Ai\Models\QnAAdvisor;
use Illuminate\Support\Facades\URL;

class GenerateWidgetEmbedCode
{
    public function handle(QnAAdvisor $qnAAdvisor): string
    {
        $scriptUrl = url('js/widgets/qna-advisor/qna-advisor-widget.js?');

        $url = URL::to(
            URL::signedRoute(
                name: 'qna-advisor.widget',
                parameters: ['qnAAdvisor' => $qnAAdvisor],
                absolute: false,
            )
        );

        return <<<EOD
        <qna-advisor-embed url="{$url}"></qna-advisor-embed>
        <script src="{$scriptUrl}"></script>
        EOD;
    }
}
