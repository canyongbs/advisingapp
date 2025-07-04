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

namespace AdvisingApp\IntegrationOpenAi\Console\Commands;

use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\IntegrationOpenAi\Jobs\UploadAssistantFileToVectorStore;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiResponsesService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Throwable;

class UploadAssistantFilesToVectorStores extends Command
{
    use TenantAware;

    protected $signature = 'integration-open-ai:upload-assistant-files-to-vector-stores {--tenant=*}';

    protected $description = 'Uploads AI assistant files to a vector stores once they have been parsed.';

    public function handle(): void
    {
        AiAssistantFile::query()
            ->whereNotNull('parsing_results')
            ->where(fn (Builder $query) => $query
                ->whereDoesntHave('openAiVectorStore')
                ->orWhereHas('openAiVectorStore', fn (Builder $query) => $query
                    ->where('ready_until', '<=', now())
                    ->orWhereNull('ready_until')
                    ->orWhereNull('vector_store_id')))
            ->eachById(function (AiAssistantFile $file) {
                try {
                    $service = $file->assistant->model->getService();

                    if (! ($service instanceof BaseOpenAiResponsesService)) {
                        return;
                    }

                    $serviceDeploymentHash = $service->getDeploymentHash();

                    if (is_null($file->openAiVectorStore) || $file->openAiVectorStore->deployment_hash !== $serviceDeploymentHash) {
                        dispatch(new UploadAssistantFileToVectorStore($file));
                    }
                } catch (Throwable $exception) {
                    report($exception);
                }
            });
    }
}
