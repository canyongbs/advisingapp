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

use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Research\Events\ResearchRequestFinished;
use AdvisingApp\Research\Events\ResearchRequestProgress;
use AdvisingApp\Research\Models\ResearchRequest;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Throwable;

class FinishResearchRequest implements ShouldQueue
{
    use Batchable;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 3;

    public function __construct(
        protected ResearchRequest $researchRequest,
    ) {}

    public function handle(): void
    {
        if (blank($this->researchRequest->results)) {
            $this->researchRequest->results = 'The artificial intelligence service was unavailable. Please try again later.';
            $this->researchRequest->title = 'Untitled Research';
            $this->researchRequest->touch('finished_at');

            broadcast(app(ResearchRequestFinished::class, [
                'researchRequest' => $this->researchRequest,
            ]));

            broadcast(app(ResearchRequestProgress::class, [
                'researchRequest' => $this->researchRequest,
            ]));

            return;
        }

        try {
            $this->researchRequest->title = app(AiIntegratedAssistantSettings::class)
                ->getDefaultModel()
                ->getService()
                ->complete(
                    prompt: Str::limit($this->researchRequest->results, limit: 1000),
                    content: 'Generate a title for this research, in 5 words or less. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with the title:',
                );
        } catch (Throwable $exception) {
            report($exception);
        }

        if (blank($this->researchRequest->title)) {
            $this->researchRequest->title = 'Untitled Research';
        }

        $this->researchRequest->touch('finished_at');

        broadcast(app(ResearchRequestFinished::class, [
            'researchRequest' => $this->researchRequest,
        ]));

        broadcast(app(ResearchRequestProgress::class, [
            'researchRequest' => $this->researchRequest,
        ]));
    }
}
