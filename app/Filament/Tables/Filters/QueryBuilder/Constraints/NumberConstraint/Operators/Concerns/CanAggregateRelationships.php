<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns;

use Exception;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;

trait CanAggregateRelationships
{
    public const AGGREGATE_SELECT_NAME = 'aggregate';

    public const AGGREGATE_AVERAGE = 'avg';

    public const AGGREGATE_MAX = 'max';

    public const AGGREGATE_MIN = 'min';

    public const AGGREGATE_SUM = 'sum';

    public function queriesRelationshipsUsingSubSelect(): bool
    {
        return parent::queriesRelationshipsUsingSubSelect() && blank($this->getSettings()[static::AGGREGATE_SELECT_NAME]);
    }

    public function applyToBaseFilterQuery(Builder $query): Builder
    {
        if (filled($this->getAggregate())) {
            $aggregateAlias = $this->generateAggregateAlias();

            if ($this->getConstraint()->isExistingAggregateAlias($aggregateAlias)) {
                return $query;
            }

            $relationship = $query->getModel()->{$this->getConstraint()->getRelationshipName()}();
            $relatedModel = $relationship->getModel();
            $relatedQuery = $relatedModel->newQuery();

            $qualifiedColumn = $relatedQuery->qualifyColumn($this->getConstraint()->getAttributeForQuery());

            $query->leftJoinSub(
                $relatedQuery
                    ->groupBy($relatedModel->getQualifiedKeyName())
                    ->selectRaw("{$relatedModel->getQualifiedKeyName()}, {$this->getAggregate()}({$qualifiedColumn}) as {$aggregateAlias}"),
                $aggregateAlias,
                fn (JoinClause $join) => $join->on("{$aggregateAlias}.{$relatedModel->getKeyName()}", '=', $query->getModel()->getQualifiedKeyName())
            );

            $this->getConstraint()->reportAggregateAlias($aggregateAlias);
        }

        return $query;
    }

    public function getConstraint(): ?NumberConstraint
    {
        $constraint = parent::getConstraint();

        if (! ($constraint instanceof NumberConstraint)) {
            throw new Exception('Constraint must be an instance of [' . NumberConstraint::class . '].');
        }

        return $constraint;
    }

    protected function getAggregateSelect(): Select
    {
        return Select::make(static::AGGREGATE_SELECT_NAME)
            ->label(__('filament-tables::filters/query-builder.operators.number.form.aggregate.label'))
            ->options([
                static::AGGREGATE_AVERAGE => __('filament-tables::filters/query-builder.operators.number.aggregates.average.label'),
                static::AGGREGATE_MAX => __('filament-tables::filters/query-builder.operators.number.aggregates.min.label'),
                static::AGGREGATE_MIN => __('filament-tables::filters/query-builder.operators.number.aggregates.max.label'),
                static::AGGREGATE_SUM => __('filament-tables::filters/query-builder.operators.number.aggregates.sum.label'),
            ])
            ->visible($this->getConstraint()->queriesRelationships());
    }

    protected function getAggregate(): ?string
    {
        return $this->getSettings()[static::AGGREGATE_SELECT_NAME] ?? null;
    }

    protected function getAttributeLabel(): string
    {
        $attributeLabel = $this->getConstraint()->getAttributeLabel();

        return __(match ($this->getAggregate()) {
            static::AGGREGATE_AVERAGE => 'filament-tables::filters/query-builder.operators.number.aggregates.average.summary',
            static::AGGREGATE_MAX => 'filament-tables::filters/query-builder.operators.number.aggregates.max.summary',
            static::AGGREGATE_MIN => 'filament-tables::filters/query-builder.operators.number.aggregates.min.summary',
            static::AGGREGATE_SUM => 'filament-tables::filters/query-builder.operators.number.aggregates.sum.summary',
            default => $attributeLabel,
        }, ['attribute' => $attributeLabel]);
    }

    protected function generateAggregateAlias(): string
    {
        $relationshipName = Str::snake($this->getConstraint()->getRelationshipName());

        return "{$relationshipName}_{$this->getAggregate()}_{$this->getConstraint()->getAttributeForQuery()}";
    }

    protected function replaceQualifiedColumnWithQualifiedAggregateColumn(string $qualifiedColumn): string
    {
        if (blank($this->getAggregate())) {
            return $qualifiedColumn;
        }

        $aggregateAlias = $this->generateAggregateAlias();

        return "{$aggregateAlias}.{$aggregateAlias}";
    }
}
