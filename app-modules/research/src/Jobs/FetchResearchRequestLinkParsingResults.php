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
use AdvisingApp\Research\Events\ResearchRequestLinkParsed;
use AdvisingApp\Research\Events\ResearchRequestProgress;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedLink;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchResearchRequestLinkParsingResults implements ShouldQueue
{
    use Batchable;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 60;

    public function __construct(
        protected ResearchRequest $researchRequest,
        protected string $link,
    ) {}

    public function handle(): void
    {
        $response = Http::withToken(app(AiIntegrationsSettings::class)->jina_deepsearch_v1_api_key)
            ->withHeaders([
                'X-Retain-Images' => 'none',
            ])
            ->get("https://r.jina.ai/{$this->link}");

        if (! $response->successful()) {
            $this->release();

            return;
        }

        $researchRequestParsedLink = new ResearchRequestParsedLink();
        $researchRequestParsedLink->researchRequest()->associate($this->researchRequest);
        $researchRequestParsedLink->results = $response->body();
        $researchRequestParsedLink->url = $this->link;
        $researchRequestParsedLink->save();

        broadcast(new ResearchRequestLinkParsed(
            researchRequest: $this->researchRequest,
            parsedLink: $researchRequestParsedLink,
        ));

        broadcast(new ResearchRequestProgress(
            researchRequest: $this->researchRequest,
        ));
    }
}
