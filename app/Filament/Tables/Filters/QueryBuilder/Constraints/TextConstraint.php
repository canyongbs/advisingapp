<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextRule\Operators\ContainsOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextRule\Operators\EndsWithOperator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\TextRule\Operators\StartsWithOperator;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class TextConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-language');

        $this->operators([
            ContainsOperator::class,
            StartsWithOperator::class,
            EndsWithOperator::class,
        ]);
    }

    public function block()
    {
        Block::make('name')
            ->label(function (?array $state) {
                $operator = str_replace('_', ' ', $state['operator'] ?? null);
                $substring = $state['substring'] ?? null;

                if (blank($operator) || blank($substring)) {
                    return 'Name';
                }

                return ($state['not'] ? 'NOT ' : '') . "name {$operator} '{$substring}'";
            })
            ->schema(fn (): array => [
                Select::make('operator')
                    ->options([
                        'contains' => 'Contains',
                        'starts_with' => 'Starts with',
                        'ends_with' => 'Ends with',
                    ]),
                TextInput::make('substring'),
                Toggle::make('not')
                    ->label('NOT')
                    ->inline(false),
            ])
            ->columns(3);
    }
}
