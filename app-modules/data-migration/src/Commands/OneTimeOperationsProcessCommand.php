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

namespace AdvisingApp\DataMigration\Commands;

use Throwable;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Multitenancy\TenantCollection;
use Illuminate\Contracts\Console\Isolatable;
use AdvisingApp\DataMigration\Models\Operation;
use AdvisingApp\DataMigration\OneTimeOperationFile;
use AdvisingApp\DataMigration\OneTimeOperationManager;
use AdvisingApp\DataMigration\Jobs\TenantOneTimeOperationProcessJob;
use AdvisingApp\DataMigration\Jobs\LandlordOneTimeOperationProcessJob;

class OneTimeOperationsProcessCommand extends OneTimeOperationsCommand implements Isolatable
{
    protected $signature = 'operations:process
                            {type : Type of operation to process: landlord|tenant}
                            {name? : Name of specific operation}
                            {--test : Process operation without tagging it as processed, so you can call it again}
                            {--async : Ignore setting in operation and process all operations asynchronously}
                            {--sync : Ignore setting in operation and process all operations synchronously}
                            {--queue= : Set the queue, that all jobs will be dispatched to}
                            {--tag=* : Process only operations, that have one of the given tag}
                            {--tenant=* : Process the operation for specific or all tenants}';

    protected $description = 'Process all unprocessed one-time operations';

    protected bool $forceAsync = false;

    protected bool $forceSync = false;

    protected ?string $queue = null;

    protected array $tags = [];

    public function handle(): int
    {
        $this->displayTestModeWarning();

        $this->forceAsync = (bool) $this->option('async');
        $this->forceSync = (bool) $this->option('sync');
        $this->queue = $this->option('queue');
        $this->tags = $this->option('tag');

        if (! $this->typeArgumentIsValid()) {
            $this->components->error('Abort! Type must be either landlord or tenant!');

            return self::FAILURE;
        }

        if (! $this->tagOptionsAreValid()) {
            $this->components->error('Abort! Do not provide empty tags!');

            return self::FAILURE;
        }

        if (! $this->syncOptionsAreValid()) {
            $this->components->error('Abort! Process either with --sync or --async.');

            return self::FAILURE;
        }

        return match ($this->argument('type')) {
            'landlord' => $this->process(),
            'tenant' => $this->processForTenants(),
        };
    }

    protected function process(): int
    {
        if ($operationName = $this->argument('name')) {
            return $this->processSingleOperation($operationName);
        }

        return $this->processNextOperations();
    }

    protected function processForTenants(): int
    {
        /** @var TenantCollection $tenants */
        $tenants = empty($this->option('tenant')) ? Tenant::all() : Tenant::whereIn('id', $this->option('tenant'))->get();

        $tenants->eachCurrent(function (Tenant $tenant) {
            $this->info(sprintf('Processing operations for tenant %s', $tenant->getKey()));

            $this->process();
        });

        return self::SUCCESS;
    }

    protected function typeArgumentIsValid(): bool
    {
        return in_array($this->argument('type'), ['landlord', 'tenant']);
    }

    protected function processSingleOperation(string $providedOperationName): int
    {
        $providedOperationName = str($providedOperationName)->rtrim('.php')->toString();

        try {
            if ($operationModel = OneTimeOperationManager::getModelByName($providedOperationName)) {
                return $this->processOperationModel($operationModel);
            }

            $operationsFile = OneTimeOperationManager::getOperationFileByName($providedOperationName);

            return $this->processOperationFile($operationsFile);
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }
    }

    protected function processOperationFile(OneTimeOperationFile $operationFile): int
    {
        $this->components->task($operationFile->getOperationName(), function () use ($operationFile) {
            $operation = $this->storeOperation($operationFile);
            $this->dispatchOperationJob($operationFile, $operation);
        });

        $this->newLine();
        $this->components->info('Processing finished.');

        return self::SUCCESS;
    }

    protected function processOperationModel(Operation $operationModel): int
    {
        if (! $this->components->confirm('Operation was processed before. Process it again?')) {
            $this->components->info('Operation aborted');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Processing operation %s.', $operationModel->name));

        $this->components->task($operationModel->name, function () use ($operationModel) {
            $operationFile = OneTimeOperationManager::getOperationFileByModel($operationModel);

            $operation = $this->storeOperation($operationFile);
            $this->dispatchOperationJob($operationFile, $operation);
        });

        $this->newLine();
        $this->components->info('Processing finished.');

        return self::SUCCESS;
    }

    protected function processNextOperations(): int
    {
        $processingOutput = 'Processing operations.';
        $unprocessedOperationFiles = OneTimeOperationManager::getUnprocessedOperationFiles();

        if ($this->tags) {
            $processingOutput = sprintf('Processing operations with tags (%s)', Arr::join($this->tags, ','));
            $unprocessedOperationFiles = $this->filterOperationsByTags($unprocessedOperationFiles);
        }

        $unprocessedOperationFiles = $this->filterOperationsByType($unprocessedOperationFiles);

        if ($unprocessedOperationFiles->isEmpty()) {
            $this->components->info('No operations to process.');

            return self::SUCCESS;
        }

        $this->components->info($processingOutput);

        foreach ($unprocessedOperationFiles as $operationFile) {
            $this->components->task($operationFile->getOperationName(), function () use ($operationFile) {
                $operation = $this->storeOperation($operationFile);
                $this->dispatchOperationJob($operationFile, $operation);
            });
        }

        $this->newLine();
        $this->components->info('Processing finished.');

        return self::SUCCESS;
    }

    protected function tagMatched(OneTimeOperationFile $operationFile): bool
    {
        return in_array($operationFile->getClassObject()->getTag(), $this->tags);
    }

    protected function typeMatched(OneTimeOperationFile $operationFile): bool
    {
        return $operationFile->getClassObject()->getType()->value === $this->argument('type');
    }

    protected function storeOperation(OneTimeOperationFile $operationFile): ?Operation
    {
        if ($this->testModeEnabled()) {
            return null;
        }

        return Operation::storeOperation($operationFile->getOperationName(), $this->isAsyncMode($operationFile));
    }

    protected function dispatchOperationJob(OneTimeOperationFile $operationFile, ?Operation $operation): void
    {
        $job = match ($this->argument('type')) {
            'landlord' => new LandlordOneTimeOperationProcessJob($operationFile->getOperationName(), $operation),
            'tenant' => new TenantOneTimeOperationProcessJob($operationFile->getOperationName(), $operation),
        };

        if ($this->isAsyncMode($operationFile)) {
            $job->dispatch($operationFile->getOperationName(), $operation)->onQueue($this->getQueue($operationFile));

            return;
        }

        $job->dispatchSync($operationFile->getOperationName(), $operation);
    }

    protected function testModeEnabled(): bool
    {
        return $this->option('test');
    }

    protected function displayTestModeWarning(): void
    {
        if ($this->testModeEnabled()) {
            $this->components->warn('Test mode! Operation won\'t be tagged as `processed`');
        }
    }

    protected function isAsyncMode(OneTimeOperationFile $operationFile): bool
    {
        if ($this->forceAsync) {
            return true;
        }

        if ($this->forceSync) {
            return false;
        }

        return $operationFile->getClassObject()->isAsync();
    }

    protected function getQueue(OneTimeOperationFile $operationFile): ?string
    {
        if ($this->queue) {
            return $this->queue;
        }

        return $operationFile->getClassObject()->getQueue() ?: null;
    }

    protected function filterOperationsByTags(Collection $unprocessedOperationFiles): Collection
    {
        return $unprocessedOperationFiles->filter(function (OneTimeOperationFile $operationFile) {
            return $this->tagMatched($operationFile);
        })->collect();
    }

    protected function filterOperationsByType(Collection $unprocessedOperationFiles): Collection
    {
        return $unprocessedOperationFiles->filter(function (OneTimeOperationFile $operationFile) {
            return $this->typeMatched($operationFile);
        })->collect();
    }

    protected function tagOptionsAreValid(): bool
    {
        // no tags provided
        if (empty($this->tags)) {
            return true;
        }

        // all tags are not empty
        if (count($this->tags) === count(array_filter($this->tags))) {
            return true;
        }

        return false;
    }

    protected function syncOptionsAreValid(): bool
    {
        // do not use both options at the same time
        return ! ($this->forceAsync && $this->forceSync);
    }
}
