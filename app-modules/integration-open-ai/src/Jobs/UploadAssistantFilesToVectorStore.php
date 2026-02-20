<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\IntegrationOpenAi\Jobs;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Jobs\TenantAware;

class UploadAssistantFilesToVectorStore implements ShouldQueue, TenantAware, ShouldBeUnique
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int
     */
    public $tries = 15;

    public function __construct(
        protected AiAssistant $assistant,
    ) {}

    public function handle(): void
    {
        $service = $this->assistant->model->getService();

        if (! ($service instanceof BaseOpenAiService)) {
            return;
        }

        $parsedFiles = $this->assistant->files()->whereNotNull('parsing_results')->get()->all();
        $parsedLinks = $this->assistant->links()->whereNotNull('parsing_results')->get()->all();

        $parsedData = [
            ...$parsedFiles,
            ...$parsedLinks,
            ...$this->assistant->getResourceHubArticles(),
        ];

        if ($parsedData && (! $service->areFilesReady($parsedData, $this->assistant))) {
            Log::info("The AI assistant [{$this->assistant->getKey()}] files are not ready for use yet.");

            ($this->attempts() < 15) && $this->release(now()->addMinute());

            return;
        }

        if ($this->assistant->files()->whereNull('parsing_results')->where('created_at', '<=', now()->subMinutes(15))->exists()) {
            Log::info("The AI assistant [{$this->assistant->getKey()}] has files that are not parsed yet.");

            ($this->attempts() < 15) && $this->release(now()->addMinute());

            return;
        }

        if ($this->assistant->links()->whereNull('parsing_results')->where('created_at', '<=', now()->subMinutes(15))->exists()) {
            Log::info("The AI assistant [{$this->assistant->getKey()}] has links that are not parsed yet.");

            ($this->attempts() < 15) && $this->release(now()->addMinute());

            return;
        }
    }

    public function uniqueId(): string
    {
        return $this->assistant->getKey();
    }
}
