<?php

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
