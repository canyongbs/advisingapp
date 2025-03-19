<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Data;

class StudentDataImportProgress extends Data
{
    public function __construct(
        public int $processed,
        public int $total,
        public int $successful,
        public string $failedRowsCsvUrl,
    ) {}

    public function getSuccessfulPercentage(): float
    {
        if ($this->total <= 0) {
            return 0;
        }

        $percentage = ($this->successful / $this->total) * 100;

        if ($percentage > 100) {
            return 100;
        }

        return $percentage;
    }

    public function getFailed(): int
    {
        return $this->processed - $this->successful;
    }

    public function getFailedPercentage(): float
    {
        if ($this->total <= 0) {
            return 0;
        }

        $percentage = (($this->processed - $this->successful) / $this->total) * 100;

        if ($percentage > 100) {
            return 100;
        }

        return $percentage;
    }

    public function getPercentage(): float
    {
        if ($this->total <= 0) {
            return 0;
        }

        $percentage = ($this->processed / $this->total) * 100;

        if ($percentage > 100) {
            return 100;
        }

        return $percentage;
    }
}
