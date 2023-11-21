<?php

namespace App\Forms\Components;

use Filament\Support\Colors\Color;
use Filament\Forms\Components\Select;

class ColorSelect extends Select
{
    protected int $shade = 600;

    protected function setUp(): void
    {
        parent::setUp();

        $this->allowHtml()
            ->native(false)
            ->shade($this->getShade());
    }

    public function shade(int $shade): static
    {
        $this->shade = $shade;

        $this->options(
            collect(Color::all())
                ->keys()
                ->sort()
                ->mapWithKeys(fn (string $color) => [
                    $color => "<span class='flex items-center gap-x-4'>
                            <span class='rounded-full w-4 h-4' style='background:rgb(" . Color::all()[$color][$shade] . ")'></span>
                            <span>" . str($color)->headline() . '</span>
                            </span>',
                ])
        );

        return $this;
    }

    public function getShade(): int
    {
        return $this->shade;
    }
}
