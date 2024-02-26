<?php

use AdvisingApp\DataMigration\OneTimeOperation;

return new class () extends OneTimeOperation {
    /**
     * Determine if the operation is being processed asynchronously.
     *
     * @return bool
     */
    protected bool $async = false;

    /**
     * Process the operation.
     */
    public function process(): void {}
};
