<?php

namespace App\Filament\Tables\Filters;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Filters\BaseFilter;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use App\Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use App\Filament\Tables\Filters\QueryBuilder\Forms\Components\RuleBuilder;

class QueryBuilder extends BaseFilter
{
    use HasConstraints;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(fn (QueryBuilder $filter): array => [
            Fieldset::make($filter->getLabel())
                ->schema([
                    RuleBuilder::make('rules')
                        ->constraints($filter->getConstraints()),
                    Checkbox::make('not')
                        ->label('Exclude these filters (NOT)'),
                ])
                ->columns(1),
        ]);

        $this->query(function (Builder $query, array $data) {
            $ruleBuilder = $this->getForm()->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder);

            $query->{($data['not'] ?? false) ? 'whereNot' : 'where'}(function (Builder $query) use ($data, $ruleBuilder) {
                $this->applyRulesToQuery($query, $data['rules'], $ruleBuilder);
            });
        });

        $this->columnSpanFull();
    }

    public static function getDefaultName(): ?string
    {
        return 'queryBuilder';
    }

    public function applyRulesToQuery(Builder $query, array $rules, RuleBuilder $ruleBuilder): Builder
    {
        foreach ($rules as $ruleIndex => $rule) {
            $ruleBuilderBlockContainer = $ruleBuilder->getChildComponentContainer($ruleIndex);

            if ($rule['type'] === 'or') {
                $query->{$rule['data']['not'] ?? false ? 'whereNot' : 'where'}(function (Builder $query) use ($rule, $ruleBuilderBlockContainer) {
                    $isFirst = true;

                    foreach ($rule['data']['groups'] as $orGroupIndex => $orGroup) {
                        $query->{match ([$isFirst, ($orGroup['not'] ?? false)]) {
                            [true, false] => 'where',
                            [true, true] => 'whereNot',
                            [false, false] => 'orWhere',
                            [false, true] => 'orWhereNot',
                        }}(function (Builder $query) use ($orGroup, $orGroupIndex, $ruleBuilderBlockContainer) {
                            $this->applyRulesToQuery(
                                $query,
                                $orGroup['rules'],
                                $ruleBuilderBlockContainer
                                    ->getComponent(fn (Component $component): bool => $component instanceof Repeater)
                                    ->getChildComponentContainer($orGroupIndex)
                                    ->getComponent(fn (Component $component): bool => $component instanceof RuleBuilder),
                            );
                        });

                        $isFirst = false;
                    }
                });

                continue;
            }

            $constraint = $this->getConstraint($rule['type']);

            if (! $constraint) {
                continue;
            }

            $operator = $rule['data'][$constraint::OPERATOR_SELECT_NAME];

            if (blank($operator)) {
                continue;
            }

            [$operatorName, $isInverseOperator] = $constraint->parseOperatorString($operator);

            $operator = $constraint->getOperator($operatorName);

            if (! $operator) {
                continue;
            }

            try {
                $ruleBuilderBlockContainer->validate();
            } catch (ValidationException) {
                continue;
            }

            $constraint
                ->settings($rule['data']['settings'])
                ->inverse($isInverseOperator);

            $operator
                ->constraint($constraint)
                ->settings($rule['data']['settings'])
                ->inverse($isInverseOperator);

            $operator->applyToBaseQuery($query);

            $constraint
                ->settings(null)
                ->inverse(null);

            $operator
                ->constraint(null)
                ->settings(null)
                ->inverse(null);
        }

        return $query;
    }
}
