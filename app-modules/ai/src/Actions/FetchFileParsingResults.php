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

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use Illuminate\Support\Facades\Http;

class FetchFileParsingResults
{
    public function __construct(
        protected AiIntegrationsSettings $aiIntegrationsSettings,
    ) {}

    public function execute(string $fileId, string $mimeType): ?string
    {
        // TODO: Check the status of the file parsing job, if it is not completed, return null. If it is in an ERROR state then throw an exeception that upstream needs to handle.

        $outputFormat = match (true) {
            str($mimeType)->startsWith(['audio/', 'video/']) => 'text',
            default => 'markdown',
        };

        $response = Http::withToken(app(AiIntegrationsSettings::class)->llamaparse_api_key)
            ->get("https://api.cloud.llamaindex.ai/api/v1/parsing/job/{$fileId}/result/{$outputFormat}");

        if ((! $response->successful()) || blank($response->json($outputFormat))) {
            return null;
        }

        return $response->json($outputFormat);
    }
}
