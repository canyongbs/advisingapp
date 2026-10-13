<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\AttributeTextConstraint\Operators;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * One selectable attribute of an AttributeTextConstraint, compared using Filament's standard text operators.
 */
class AttributeTextOperator extends Operator
{
    public const TEXT_OPERATOR_SETTING = 'operator';

    public const TEXT_SETTING = 'text';

    protected ?string $column = null;

    protected ?string $relationship = null;

    public function column(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function relationship(string $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->getName();
    }

    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function getSummary(): string
    {
        return $this->getTextOperator()?->getSummary() ?? $this->getLabel();
    }

    public function getFormSchema(): array
    {
        $textOperatorOptions = $this->makeTextConstraint()->getOperatorSelectOptions();

        return [
            Select::make(static::TEXT_OPERATOR_SETTING)
                ->label('Operator')
                ->options($textOperatorOptions)
                ->default(array_key_first($textOperatorOptions))
                ->required(),
            TextInput::make(static::TEXT_SETTING)
                ->label('Value')
                ->required(),
        ];
    }

    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function applyToBaseQuery(Builder $query): Builder
    {
        $textOperator = $this->getTextOperator();

        if (! $textOperator) {
            return $query;
        }

        if (blank($this->getRelationship())) {
            return $this->applyTextOperator($query, $textOperator);
        }

        return $query->whereHas(
            $this->getRelationship(),
            fn (Builder $query): Builder => $this->applyTextOperator($query, $textOperator),
        );
    }

    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    protected function applyTextOperator(Builder $query, Operator $textOperator): Builder
    {
        return $textOperator->apply($query, $query->qualifyColumn($this->getColumn()));
    }

    /**
     * Returns null for incomplete or tampered settings, so the rule is skipped like any invalid Filament rule.
     */
    protected function getTextOperator(): ?Operator
    {
        $operatorString = $this->getStringSetting(static::TEXT_OPERATOR_SETTING);
        $text = $this->getStringSetting(static::TEXT_SETTING);

        if (blank($operatorString) || ($text === null)) {
            return null;
        }

        $textConstraint = $this->makeTextConstraint();

        [$operatorName, $isInverse] = $textConstraint->parseOperatorString($operatorString);

        return $textConstraint->getOperator($operatorName)
            ?->constraint($textConstraint)
            ->settings([static::TEXT_SETTING => $text])
            ->inverse($isInverse);
    }

    protected function makeTextConstraint(): TextConstraint
    {
        return TextConstraint::make($this->getName())->label($this->getLabel());
    }
}
