<?php

namespace AdvisingApp\StockMedia\Enums;

use Filament\Support\Contracts\HasLabel;

enum StockMediaProvider: string implements HasLabel
{
    case Pexels = 'pexels';

    public function getLabel(): string 
    {
        return $this->name;
    }
}
