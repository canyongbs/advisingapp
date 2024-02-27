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
use Illuminate\Support\Collection;
use Spatie\Multitenancy\TenantCollection;
use AdvisingApp\DataMigration\Models\Operation;
use AdvisingApp\DataMigration\OneTimeOperationFile;
use AdvisingApp\DataMigration\OneTimeOperationManager;
use AdvisingApp\DataMigration\Commands\Utils\OperationsLineElement;

class OneTimeOperationShowCommand extends OneTimeOperationsCommand
{
    protected $signature = 'operations:show
                            {type : Type of operation to process: landlord|tenant}
                            {filter?* : List of filters: pending|processed|disposed}
                            {--tenant=* : Process the operation for specific or all tenants}';

    protected $description = 'List of all one-time operations';

    protected array $validFilters = [
        self::LABEL_PENDING,
        self::LABEL_PROCESSED,
        self::LABEL_DISPOSED,
    ];

    public function handle(): int
    {
        try {
            $this->validateFilters();
            $this->newLine();

            match ($this->argument('type')) {
                'landlord' => $this->outputOperations(),
                'tenant' => $this->outputOperationsForTenants(),
            };

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }
    }

    protected function outputOperations(): void
    {
        $operationOutputLines = $this->getOperationLinesForOutput();
        $operationOutputLines = $this->filterOperationLinesByStatus($operationOutputLines);

        if ($operationOutputLines->isEmpty()) {
            $this->components->info('No operations found.');
        }

        /** @var OperationsLineElement $lineElement */
        foreach ($operationOutputLines as $lineElement) {
            $lineElement->output($this->components);
        }

        $this->newLine();
    }

    protected function outputOperationsForTenants(): void
    {
        /** @var TenantCollection $tenants */
        $tenants = empty($this->option('tenant')) ? Tenant::all() : Tenant::whereIn('id', $this->option('tenant'))->get();

        $tenants->eachCurrent(function (Tenant $tenant) {
            $this->info(sprintf('Operations for tenant %s', $tenant->getKey()));

            $this->newLine();

            $this->outputOperations();
        });
    }

    /**
     * @throws Throwable
     */
    protected function validateFilters(): void
    {
        $filters = array_map(fn ($filter) => strtolower($filter), $this->argument('filter'));
        $validFilters = array_map(fn ($filter) => strtolower($filter), $this->validFilters);

        throw_if(array_diff($filters, $validFilters), \Exception::class, 'Given filter is not valid. Allowed filters: ' . implode('|', array_map('strtolower', $this->validFilters)));
    }

    protected function shouldDisplayByFilter(string $filterName): bool
    {
        $givenFilters = $this->argument('filter');

        if (empty($givenFilters)) {
            return true;
        }

        $givenFilters = array_map(fn ($filter) => strtolower($filter), $givenFilters);

        return in_array(strtolower($filterName), $givenFilters);
    }

    protected function getOperationLinesForOutput(): Collection
    {
        $operationModels = Operation::all();
        $operationFiles = OneTimeOperationManager::getAllOperationFiles();
        $operationOutputLines = collect();

        $operationFiles = $this->filterOperationsByType($operationFiles);

        // add disposed operations
        foreach ($operationModels as $operation) {
            if (OneTimeOperationManager::fileExistsByName($operation->name)) {
                continue;
            }

            $operationOutputLines->add(OperationsLineElement::make($operation->name, self::LABEL_DISPOSED, $operation->processed_at));
        }

        // add processed and pending operations
        foreach ($operationFiles->toArray() as $file) {
            /** @var OneTimeOperationFile $file */
            if ($model = $file->getModel()) {
                $operationOutputLines->add(OperationsLineElement::make($model->name, self::LABEL_PROCESSED, $model->processed_at, $file->getClassObject()->getTag()));
            } else {
                $operationOutputLines->add(OperationsLineElement::make($file->getOperationName(), self::LABEL_PENDING, null, $file->getClassObject()->getTag()));
            }
        }

        return $operationOutputLines;
    }

    protected function filterOperationLinesByStatus(Collection $operationOutputLines): Collection
    {
        return $operationOutputLines->filter(function (OperationsLineElement $lineElement) {
            return $this->shouldDisplayByFilter($lineElement->getStatus());
        })->collect();
    }

    protected function filterOperationsByType(Collection $unprocessedOperationFiles): Collection
    {
        return $unprocessedOperationFiles->filter(function (OneTimeOperationFile $operationFile) {
            return $this->typeMatched($operationFile);
        })->collect();
    }

    protected function typeMatched(OneTimeOperationFile $operationFile): bool
    {
        return $operationFile->getClassObject()->getType()->value === $this->argument('type');
    }
}
