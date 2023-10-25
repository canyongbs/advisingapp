<?php

namespace Assist\Campaign\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

abstract class CampaignActionBlock extends Block
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function make(string $name = null): static
    {
        return parent::make($name ?? static::type());
    }

    public function createFields(): array
    {
        return $this->generateFields();
    }

    public function editFields(): array
    {
        return $this->generateFields('data.');
    }

    abstract public static function type(): string;
}
