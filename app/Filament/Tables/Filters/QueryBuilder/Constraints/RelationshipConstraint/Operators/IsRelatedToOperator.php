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

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Closure;
use Exception;
use Illuminate\Support\Arr;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;

class IsRelatedToOperator extends Operator
{
    protected string | Closure | null $titleAttribute = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected bool | Closure $isPreloaded = false;

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isNative = true;

    protected bool | Closure $isStatic = false;

    protected bool | Closure $isSearchable = false;

    protected int | Closure $optionsLimit = 50;

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    public function getName(): string
    {
        return 'isRelatedTo';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.is_related_to.label.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.is_related_to.label.direct',
        );
    }

    public function getSummary(): string
    {
        $constraint = $this->getConstraint();

        $values = Arr::wrap($this->getSettings()[$constraint->isMultiple() ? 'values' : 'value']);

        $relationshipQuery = $this->getRelationshipQuery();

        $values = $relationshipQuery
            ->whereKey($values)
            ->pluck($relationshipQuery->qualifyColumn($this->getTitleAttribute()))
            ->join(glue: __('filament-tables::filters/query-builder.operators.relationship.is_related_to.summary.values_glue.0'), finalGlue: __('filament-tables::filters/query-builder.operators.relationship.is_related_to.summary.values_glue.final'));

        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.is_related_to.summary.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.is_related_to.summary.direct',
            [
                'relationship' => $constraint->getAttributeLabel(),
                'values' => $values,
            ],
        );
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        $constraint = $this->getConstraint();

        $field = Select::make($constraint->isMultiple() ? 'values' : 'value')
            ->label(__($constraint->isMultiple() ? 'filament-tables::filters/query-builder.operators.relationship.is_related_to.form.values.label' : 'filament-tables::filters/query-builder.operators.relationship.is_related_to.form.value.label'))
            ->multiple($this->isMultiple())
            ->searchable($this->isSearchable())
            ->preload($this->isPreloaded())
            ->native($this->isNative())
            ->optionsLimit($this->getOptionsLimit())
            ->required()
            ->relationship(
                $constraint->getRelationshipName(),
                $this->getTitleAttribute(),
                $this->modifyRelationshipQueryUsing,
            )
            ->forceSearchCaseInsensitive($this->isSearchForcedCaseInsensitive());

        if ($this->getOptionLabelUsing) {
            $field->getOptionLabelUsing($this->getOptionLabelUsing);
        }

        if ($this->getOptionLabelsUsing) {
            $field->getOptionLabelsUsing($this->getOptionLabelsUsing);
        }

        if ($this->getOptionLabelFromRecordUsing) {
            $field->getOptionLabelFromRecordUsing($this->getOptionLabelFromRecordUsing);
        }

        if ($this->getSearchResultsUsing) {
            $field->getSearchResultsUsing($this->getSearchResultsUsing);
        }

        return [$field];
    }

    public function titleAttribute(string | Closure | null $attribute): static
    {
        $this->titleAttribute = $attribute;

        return $this;
    }

    public function modifyRelationshipQueryUsing(?Closure $modifyQueryUsing = null): static
    {
        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;

        return $this;
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $constraint = $this->getConstraint();

        $value = $this->getSettings()[$constraint->isMultiple() ? 'values' : 'value'];

        return $query->{$this->isInverse() ? 'whereDoesntHave' : 'whereHas'}(
            $constraint->getRelationshipName(),
            fn (Builder $query) => $query->whereKey($value),
        );
    }

    public function getConstraint(): ?RelationshipConstraint
    {
        $constraint = parent::getConstraint();

        if (! ($constraint instanceof RelationshipConstraint)) {
            throw new Exception('Is operator can only be used with relationship constraints.');
        }

        return $constraint;
    }

    public function forceSearchCaseInsensitive(bool | Closure | null $condition = true): static
    {
        $this->isSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    public function isSearchForcedCaseInsensitive(): ?bool
    {
        return $this->evaluate($this->isSearchForcedCaseInsensitive);
    }

    public function getModifyRelationshipQueryUsing(): ?Closure
    {
        return $this->modifyRelationshipQueryUsing;
    }

    public function getRelationship(): Relation | Builder
    {
        $constraint = $this->getConstraint();

        $record = app($constraint->getFilter()->getTable()->getModel());

        $relationship = null;

        foreach (explode('.', $constraint->getRelationshipName()) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    public function getRelationshipQuery(): ?Builder
    {
        $relationship = Relation::noConstraints(fn () => $this->getRelationship());

        $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

        if ($this->getModifyRelationshipQueryUsing()) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        if (empty($relationshipQuery->getQuery()->orders)) {
            $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($this->getTitleAttribute()));
        }

        return $relationshipQuery;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->evaluate($this->titleAttribute);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function native(bool | Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function preload(bool | Closure $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function isPreloaded(): bool
    {
        return (bool) $this->evaluate($this->isPreloaded);
    }
}
