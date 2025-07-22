<?php

namespace AdvisingApp\Workflow\Filament\Blocks;

use Filament\Forms\Components\Select;

class EventBlock extends WorkflowActionBlock
{
    //TODO: implement
    public function generateFields(): array
    {
        return [
            Select::make('temp'),
        ];
    }

    public static function type(): string
    {
        return 'event';
    }
}
