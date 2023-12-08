<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Actions\Imports\Models\Import as BaseImport;

class Import extends BaseImport
{
    use HasUuids;
}
