<?php

namespace Assist\Campaign\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

class CampaignActionBlock extends Block
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function make(string $name = null): static
    {
        return parent::make($name ?? static::type());
    }
}
