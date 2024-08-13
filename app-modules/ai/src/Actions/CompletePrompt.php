<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Support\Arr;
use Laravel\Pennant\Feature;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiFeature;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;

class CompletePrompt
{
    public function __invoke(AiModel $aiModel, string $prompt, string $content): string
    {
        $service = $aiModel->getService();

        $completion = $service->complete($prompt, $content);

        LegacyAiMessageLog::create([
            'message' => $content,
            'metadata' => [
                'prompt' => $prompt,
                'completion' => $completion,
            ],
            'request' => [
                'headers' => Arr::only(
                    request()->headers->all(),
                    ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
                ),
                'ip' => request()->ip(),
            ],
            'sent_at' => now(),
            'user_id' => auth()->id(),
            ...Feature::active('ai-assistant-auditing-changes') ? ['ai_assistant_name' => 'Institutional Assistant'] : [],
            ...Feature::active('ai-log-features') ? ['feature' => AiFeature::DraftWithAi] : [],
        ]);

        return $completion;
    }
}
