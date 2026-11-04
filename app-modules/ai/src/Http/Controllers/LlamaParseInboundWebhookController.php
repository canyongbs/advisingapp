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

namespace AdvisingApp\Ai\Http\Controllers;

use AdvisingApp\Ai\Jobs\FetchAiLinkParsingResults;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use App\Features\LlamaParseLinkParsingFeature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LlamaParseInboundWebhookController
{
    /**
     * Responds straight away, as LlamaParse only waits a few seconds for each delivery. The queued job fetches the
     * results, so every event type is handled the same way, and a delivery for an unknown job is ignored.
     */
    public function __invoke(Request $request): Response
    {
        if (! LlamaParseLinkParsingFeature::active()) {
            // TODO: Cleanup Task (llamaparse-link-parsing): delete this check, as the parsing columns always exist once the feature flag is removed.
            return response()->noContent();
        }

        $parsingJobId = $request->string('data.job_id')->toString();

        if (blank($parsingJobId)) {
            return response()->noContent();
        }

        $link = CustomerAdvisorLink::query()->where('parsing_job_id', $parsingJobId)->first()
            ?? AiAssistantLink::query()->where('parsing_job_id', $parsingJobId)->first();

        if ($link) {
            dispatch(new FetchAiLinkParsingResults($link, $parsingJobId));
        }

        return response()->noContent();
    }
}
