<?php

namespace App\Overrides\Filament\Actions\Imports\Jobs;

use Carbon\CarbonInterface;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;

class PrepareCsvExportOverride extends PrepareCsvExport
{
    public int $tries = 2;

    public function retryUntil(): ?CarbonInterface
    {
        return null;
    }

    public function getJobQueue(): ?string
    {
        return config('queue.import_export_queue');
    }
}