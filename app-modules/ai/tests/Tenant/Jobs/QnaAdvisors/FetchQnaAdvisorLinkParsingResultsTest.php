<?php

use AdvisingApp\Ai\Jobs\QnaAdvisors\FetchQnaAdvisorLinkParsingResults;
use AdvisingApp\Ai\Models\QnaAdvisorLink;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Support\Facades\Http;

it('refreshes existing parsing results when explicitly requested', function () {
    Http::fake([
        'https://r.jina.ai/*' => Http::response('fresh parsing results', 200),
    ]);

    $settings = app(AiIntegrationsSettings::class);
    $settings->jina_deepsearch_v1_api_key = 'test-api-key';

    $link = QnaAdvisorLink::factory()->create([
        'parsing_results' => 'stale parsing results',
    ]);

    (new FetchQnaAdvisorLinkParsingResults($link, refreshExistingParsingResults: true))->handle();

    $link->refresh();

    expect($link->parsing_results)->toBe('fresh parsing results');
});
