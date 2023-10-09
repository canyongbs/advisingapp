<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Forms\Components;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

class ConditionRepeater extends Repeater
{
    protected string $view = 'filament.forms.components.condition-repeater';

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(fn (): array => [
            Checkbox::make('not')
                ->label('NOT'),
            Builder::make('conditions')
                ->blocks(fn (): array => [
                    Builder\Block::make('name')
                        ->label(function (?array $state) {
                            $operator = str_replace('_', ' ', $state['operator'] ?? null);
                            $substring = $state['substring'] ?? null;

                            if (blank($operator) || blank($substring)) {
                                return 'Name';
                            }

                            return "Name {$operator} '{$substring}'";
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
                        ->columns(3),
                    Builder\Block::make('email')
                        ->label(function (?array $state) {
                            $operator = str_replace('_', ' ', $state['operator'] ?? null);
                            $substring = $state['substring'] ?? null;

                            if (blank($operator) || blank($substring)) {
                                return 'Email address';
                            }

                            return "Email address {$operator} '{$substring}'";
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
                        ->columns(3),
                    Builder\Block::make('orGroup')
                        ->label(function (?array $state) {
                            if (blank($state)) {
                                return 'Combination';
                            }

                            return "and either";
                        })
                        ->schema(fn (): array => [
                            Checkbox::make('not')
                                ->label('NOT'),
                            static::make('conditions')
                                ->hiddenLabel(),
                        ]),
                ])
                ->addAction(fn (Action $action) => $action
                    ->label('AND')
                    ->icon('heroicon-s-plus'))
                ->addBetweenAction(fn (Action $action) => $action
                    ->label('AND')
                    ->icon('heroicon-s-plus'))
                ->hiddenLabel()
                ->blockNumbers(false)
                ->collapsed()
                ->cloneable()
                ->expandAllAction(fn (Action $action) => $action->hidden())
                ->collapseAllAction(fn (Action $action) => $action->hidden())
                ->key('conditions'),
        ]);

        $this->addAction(fn (Action $action) => $action
            ->label('OR')
            ->icon('heroicon-s-plus'));

        $this->addBetweenAction(fn (Action $action) => $action
            ->visible()
            ->label('OR')
            ->icon('heroicon-s-plus'));

        $this->collapsible();

        $this->cloneable();
    }
}
