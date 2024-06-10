<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Concerns\HasStep;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;

class Slider extends Field
{
    use HasExtraInputAttributes;
    use HasStep;

    protected string $view = 'filament.forms.components.slider';

    /**
     * @var scalar | Closure | null
     */
    protected $maxValue = null;

    /**
     * @var scalar | Closure | null
     */
    protected $minValue = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule('numeric');
    }

    /**
     * @param  scalar | Closure | null  $value
     */
    public function maxValue($value): static
    {
        $this->maxValue = $value;

        $this->rule(static function (Slider $component): string {
            $value = $component->getMaxValue();

            return "max:{$value}";
        }, static fn (Slider $component): bool => filled($component->getMaxValue()));

        return $this;
    }

    /**
     * @param  scalar | Closure | null  $value
     */
    public function minValue($value): static
    {
        $this->minValue = $value;

        $this->rule(static function (Slider $component): string {
            $value = $component->getMinValue();

            return "min:{$value}";
        }, static fn (Slider $component): bool => filled($component->getMinValue()));

        return $this;
    }

    /**
     * @return scalar | null
     */
    public function getMaxValue()
    {
        return $this->evaluate($this->maxValue);
    }

    /**
     * @return scalar | null
     */
    public function getMinValue()
    {
        return $this->evaluate($this->minValue);
    }
}
