<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Actions\Imports\Models\FailedImportRow as BaseFailedImportRow;

class FailedImportRow extends BaseFailedImportRow
{
    use HasUuids;
}
