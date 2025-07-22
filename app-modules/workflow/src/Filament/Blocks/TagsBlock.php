<?php

namespace AdvisingApp\Workflow\Filament\Blocks;

use Filament\Forms\Components\Select;

class TagsBlock extends WorkflowActionBlock
{
    //TODO: implement
    //TODO: implement
    public function generateFields(): array
    {
        return [
            Select::make('temp'),
        ];
    }

    public static function type(): string
    {
        return 'tags';
    }
}
