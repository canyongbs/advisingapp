<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ImportExportPageTab: string implements HasLabel
{
    case Import = 'import';
    case Export = 'export';
    case StudentSync = 'student_sync';

    public function getLabel(): string
    {
        return match ($this) {
            self::Import => 'Import',
            self::Export => 'Export',
            self::StudentSync => 'Student Sync',
        };
    }
}
