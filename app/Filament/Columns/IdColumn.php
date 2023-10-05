<?php

namespace App\Filament\Columns;

use Filament\Tables\Columns\TextColumn;

class IdColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('ID');

        $this->sortable();

        $this->toggleable(isToggledHiddenByDefault: true);
    }

    public static function make(string $name = 'id'): static
    {
        return parent::make($name);
    }
}
