<?php

namespace AdvisingApp\Workflow\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;

abstract class WorkflowActionBlock extends Block
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? static::type());
    }

    /**
     * @return array<int, covariant Field>
     */
    public function createFields(): array
    {
        return $this->generateFields();
    }

    /**
     * @return array<int, covariant Field>
     */
    public function editFields(): array
    {
        return $this->generateFields();
    }

    /**
     * @return array<int, covariant Field>
     */
    abstract public function generateFields(): array;

    abstract public static function type(): string;
}
