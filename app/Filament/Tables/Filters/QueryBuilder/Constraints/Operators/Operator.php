<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators;

use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Components\Component;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Concerns\CanBeHidden;

abstract class Operator extends Component
{
    use CanBeHidden;

    protected ?Constraint $constraint = null;

    protected ?array $settings = null;

    protected ?bool $isInverse = null;

    public static function make(): static
    {
        return app(static::class);
    }

    abstract public function getName(): string;

    abstract public function getLabel(): string;

    abstract public function getSummary(): string;

    public function query(Builder $query, string $qualifiedColumn): Builder
    {
        return $query;
    }

    public function baseQuery(Builder $query): Builder
    {
        $qualifiedColumn = $query->qualifyColumn($this->getConstraint()->getAttributeForQuery());

        if ($this->getConstraint()->queriesRelationships()) {
            return $query->whereHas(
                $this->getConstraint()->getRelationshipName(),
                function (Builder $query) use ($qualifiedColumn): Builder {
                    $modifyRelationshipQueryUsing = $this->getConstraint()->getModifyRelationshipQueryUsing();

                    if ($modifyRelationshipQueryUsing) {
                        $query = $this->evaluate($modifyRelationshipQueryUsing, [
                            'query' => $query,
                        ]) ?? $query;
                    }

                    return $this->query($query, $qualifiedColumn);
                },
            );
        }

        return $this->query($query, $qualifiedColumn);
    }

    public function getFormSchema(): array
    {
        return [];
    }

    public function constraint(?Constraint $constraint): static
    {
        $this->constraint = $constraint;

        return $this;
    }

    public function settings(?array $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

    public function inverse(?bool $condition = true): static
    {
        $this->isInverse = $condition;

        return $this;
    }

    public function getConstraint(): ?Constraint
    {
        return $this->constraint;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function isInverse(): ?bool
    {
        return $this->isInverse;
    }
}
