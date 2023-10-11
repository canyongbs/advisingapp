<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Rules;

use App\Filament\Tables\Filters\QueryBuilder\Rules\Concerns\HasLabel;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\HasIcon;

class Rule extends Component
{
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasOperators;
    use HasIcon;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function getBuilderBlock(): Block
    {
        return Block::make($this->getName())
            ->label($this->getLabel())
            ->icon($this->getIcon())
            ->schema(fn (): array => [
                $this->getOperatorSelect(),
            ]);
    }

    public function getOperatorSelect(): Select
    {
        return Select::make('operator')
            ->options($this->getOperatorSelectOptions());
    }

    public function getOperatorSelectOptions(): array
    {
        foreach ($this->getOperators() as $operatorName => $operator) {
            $options[$operatorName] = $operator->getLabel();
            $options["{$operatorName}_inverse"] = $operator->getInverseLabel();
        }

        return $options;
    }
}
