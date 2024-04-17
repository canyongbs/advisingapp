<?php

namespace App\Overrides\Filament\Actions\Imports\Jobs;

use Carbon\CarbonInterface;
use Filament\Actions\Imports\Jobs\ImportCsv;

class ImportCsvOverride extends ImportCsv
{
    public int $tries = 2;

    public function retryUntil(): ?CarbonInterface
    {
        return null;
    }
}
