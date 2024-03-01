<?php

use App\Models\Tenant;
use AdvisingApp\DataMigration\OneTimeOperation;
use AdvisingApp\DataMigration\Enums\OperationType;
use App\Multitenancy\DataTransferObjects\TenantConfig;

return new class () extends OneTimeOperation {
    /**
     * The type to determine where it will be run. OperationType::Tenant or OperationType::Landlord.
     */
    protected OperationType $type = OperationType::Landlord;

    /**
     * Determine if the operation is being processed asynchronously.
     */
    protected bool $async = true;

    /**
     * A tag name, that this operation can be filtered by.
     */
    protected ?string $tag = 'deployment';

    /**
     * Process the operation.
     */
    public function process(): void
    {
        Tenant::all()->each(function (Tenant $tenant) {
            $config = TenantConfig::from($tenant->config);

            if ($config->mail->fromAddress === 'hello@example.com') {
                $config->mail->fromAddress = 'no-reply@advising.app';

                $tenant->update([
                    'config' => $config,
                ]);
            }
        });
    }
};
