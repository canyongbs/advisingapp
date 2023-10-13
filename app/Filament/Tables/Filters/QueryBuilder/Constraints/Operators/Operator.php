<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators;

use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Concerns\CanBeHidden;
use Closure;
use Filament\Support\Components\Component;
use Illuminate\Database\Eloquent\Builder;

abstract class Operator extends Component
{
    use CanBeHidden;

    public static function make(): static
    {
        return app(static::class);
    }

    abstract public function getName(): string;

    abstract public function getLabel(bool $isInverse): string;

    abstract public function getSummary(Constraint $constraint, array $settings, bool $isInverse): string;

    public function query(Builder $query, string $attribute, array $settings, bool $isInverse): Builder
    {
        return $query;
    }

    public function applyToQueryForConstraint(Builder $query, Constraint $constraint, array $settings, bool $isInverse): Builder
    {
        $attribute = $query->qualifyColumn($constraint->getAttributeForQuery());

        if ($constraint->queriesRelationships()) {
            return $query->whereHas(
                $constraint->getRelationshipName(),
                function (Builder $query) use ($attribute, $constraint, $isInverse, $settings): Builder {
                    $modifyRelationshipQueryUsing = $constraint->getModifyRelationshipQueryUsing();

                    if ($modifyRelationshipQueryUsing) {
                        $query = $this->evaluate($modifyRelationshipQueryUsing, [
                            'query' => $query,
                        ]) ?? $query;
                    }

                    return $this->query($query, $attribute, $settings, $isInverse);
                },
            );
        }

        return $this->query($query, $attribute, $settings, $isInverse);
    }

    public function getFormSchema(): array
    {
        return [];
    }
}
