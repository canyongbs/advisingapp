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

use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Research\Events\ResearchRequestProgress;
use AdvisingApp\Research\Events\ResearchRequestSearchResultsParsed;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedSearchResults;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchResearchRequestSearchQueryParsingResults implements ShouldQueue
{
    use Batchable;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 60;

    public function __construct(
        protected ResearchRequest $researchRequest,
        protected string $searchQuery,
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $response = Http::withToken(app(AiIntegrationsSettings::class)->jina_deepsearch_v1_api_key)
                ->withHeaders([
                    'X-Engine' => 'direct',
                    'X-Retain-Images' => 'none',
                ])
                ->get('https://s.jina.ai', ['q' => $this->searchQuery]);

            if (! $response->successful()) {
                $this->release();

                return;
            }

            $researchRequestParsedSearchResults = new ResearchRequestParsedSearchResults();
            $researchRequestParsedSearchResults->researchRequest()->associate($this->researchRequest);
            $researchRequestParsedSearchResults->results = $response->body();
            $researchRequestParsedSearchResults->search_query = $this->searchQuery;
            $researchRequestParsedSearchResults->save();

            preg_match_all('/\[\d+\] Title: (.+?)\n\[.*?\] URL Source: (.+?)\n/s', $response->body(), $matches, PREG_SET_ORDER);

            $sources = [];

            foreach ($matches as $match) {
                $title = trim($match[1]);
                $url = trim($match[2]);
                $domain = parse_url($url, PHP_URL_HOST);

                str_replace(
                    ['\\', '[', ']'],
                    ['\\\\', '\\[', '\\]'],
                    $title,
                );

                $sources[] = "[{$title} ({$domain})]({$url})";
            }

            DB::statement(<<<SQL
                UPDATE research_requests
                SET sources = COALESCE(
                    CASE
                        WHEN jsonb_typeof(sources) = 'array'
                        THEN sources
                        ELSE '[]'::jsonb
                    END,
                    '[]'::jsonb
                ) || ?::jsonb
                WHERE id = ?
                SQL, [json_encode($sources), $this->researchRequest->id]);

            broadcast(new ResearchRequestSearchResultsParsed(
                researchRequest: $this->researchRequest,
                parsedSearchResults: $researchRequestParsedSearchResults,
                newSources: $sources,
            ));

            broadcast(new ResearchRequestProgress(
                researchRequest: $this->researchRequest,
            ));
        });
    }
}
