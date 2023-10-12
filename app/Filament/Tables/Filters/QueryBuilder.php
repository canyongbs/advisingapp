<?php

namespace App\Filament\Tables\Filters;

use App\Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use App\Filament\Tables\Filters\QueryBuilder\Forms\Components\RuleRepeater;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class QueryBuilder extends BaseFilter
{
    use HasConstraints;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(fn (QueryBuilder $filter): array => [
            RuleRepeater::make('rules')
                ->constraints($filter->getConstraints()),
        ]);

        $this->query(function (Builder $query, array $data) {
            return $this->applyRuleGroupsToQuery($query, $data['rules']);
        });

        $this->columnSpanFull();
    }

    public function applyRuleGroupsToQuery(Builder $query, array $data): Builder
    {
        $isFirst = true;

        foreach ($data as $orGroup) {
            $query->{match ([$isFirst, $orGroup['not'] ?? false]) {
                [true, false] => 'where',
                [true, true] => 'whereNot',
                [false, false] => 'orWhere',
                [false, true] => 'orWhereNot',
            }}(function (Builder $query) use ($orGroup) {
                foreach ($orGroup['rules'] as $rule) {
                    if ($rule['type'] === 'orGroup') {
                        $query->{$rule['data']['not'] ?? false ? 'whereNot' : 'where'}(function (Builder $query) use ($rule) {
                            $this->applyRuleGroupsToQuery($query, $rule['data']['groups']);
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

                    $operator->query($query, $rule['type'], $rule['data']['settings'], $isInverseOperator);
                }
            });

            $isFirst = false;
        }

        return $query;
    }
}
