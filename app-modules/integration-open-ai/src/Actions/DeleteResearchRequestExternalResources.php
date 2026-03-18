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

namespace AdvisingApp\IntegrationOpenAi\Actions;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiResearchRequestVectorStore;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService;
use AdvisingApp\Research\Models\ResearchRequest;
use Exception;

class DeleteResearchRequestExternalResources
{
    public function execute(ResearchRequest $researchRequest): void
    {
        $deploymentHashes = OpenAiResearchRequestVectorStore::query()
            ->whereBelongsTo($researchRequest)
            ->whereNotNull('vector_store_id')
            ->distinct()
            ->pluck('deployment_hash')
            ->filter()
            ->values();

        if ($deploymentHashes->isEmpty()) {
            return;
        }

        foreach ($deploymentHashes as $deploymentHash) {
            $service = $this->resolveServiceForDeploymentHash($deploymentHash);

            if (! $service) {
                report(new Exception('Unable to resolve OpenAI service while deleting external resources for research request [' . $researchRequest->getKey() . '] and deployment hash [' . $deploymentHash . '].'));

                continue;
            }

            try {
                $service->deleteResearchRequestExternalResources($researchRequest);
            } catch (Exception $exception) {
                report($exception);
            }
        }
    }

    private function resolveServiceForDeploymentHash(string $deploymentHash): ?BaseOpenAiService
    {
        foreach ($this->getConfiguredOpenAiServices() as $service) {
            if ($service->getDeploymentHash() === $deploymentHash) {
                return $service;
            }
        }

        return null;
    }

    /**
     * @return array<BaseOpenAiService>
     */
    private function getConfiguredOpenAiServices(): array
    {
        $services = [];

        foreach (AiModel::cases() as $model) {
            if (! $model->hasService()) {
                continue;
            }

            $serviceClass = $model->getServiceClass();

            if (! is_subclass_of($serviceClass, BaseOpenAiService::class)) {
                continue;
            }

            if (array_key_exists($serviceClass, $services)) {
                continue;
            }

            /** @var BaseOpenAiService $service */
            $service = app($serviceClass);

            if (! filled($service->getApiKey()) || ! filled($service->getDeployment())) {
                continue;
            }

            $services[$serviceClass] = $service;
        }

        return array_values($services);
    }
}
