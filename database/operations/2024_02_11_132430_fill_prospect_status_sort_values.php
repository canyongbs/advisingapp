<?php

use Laravel\Pennant\Feature;
use App\Features\ProspectStatusSortFeature;
use AdvisingApp\DataMigration\OneTimeOperation;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\DataMigration\Enums\OperationType;

return new class () extends OneTimeOperation {
    /**
     * The type to determine where it will be run. OperationType::Tenant or OperationType::Landlord.
     */
    protected OperationType $type = OperationType::Tenant;

    /**
     * Determine if the operation is being processed asynchronously.
     */
    protected bool $async = true;

    /**
     * The queue that the job will be dispatched to. Will default to defaults in config.
     */
    protected ?string $queue = null;

    /**
     * A tag name, that this operation can be filtered by.
     */
    protected ?string $tag = 'after-deployment';

    /**
     * Process the operation.
     */
    public function process(): void
    {
        $sort = 1;

        ProspectStatus::query()->orderBy('sort')->each(function (ProspectStatus $status) use (&$sort) {
            $status->update(['sort' => $sort]);

            $sort++;
        });

        Feature::activate(ProspectStatusSortFeature::class);
    }
};
