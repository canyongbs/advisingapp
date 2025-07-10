<?php

declare(strict_types = 1);

namespace App\Overrides\LaravelSqsExtended;

use DefectiveCode\LaravelSqsExtended\SqsDiskJob as PackageSqsDiskJob;
use Override;
use TypeError;

class SqsDiskJob extends PackageSqsDiskJob
{
    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    #[Override]
    public function getRawBody()
    {
        try {
            return parent::getRawBody();
        } catch (TypeError $error) {
            report($error);

            /*
             *  If we are unable to retrieve the raw body with this known error type,
             *  we should remove the item from the queue.
             */
            $this->delete();

            throw $error;
        }
    }
}
