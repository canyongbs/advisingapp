<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Components\Component;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Concerns\CanBeHidden;

class Operator extends Component
{
    use CanBeHidden;

    protected string $evaluationIdentifier = 'operator';

    protected ?Constraint $constraint = null;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $settings = null;

    protected ?bool $isInverse = null;

    protected ?string $name = null;

    protected string | Closure | null $label = null;

    protected string | Closure | null $summary = null;

    protected ?Closure $modifyQueryUsing = null;

    protected ?Closure $modifyBaseQueryUsing = null;

    final public function __construct(?string $name = null)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function name(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function summary(string | Closure | null $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function query(?Closure $callback): static
    {
        $this->modifyQueryUsing($callback);

        return $this;
    }

    public function baseQuery(?Closure $callback): static
    {
        $this->modifyBaseQueryUsing($callback);

        return $this;
    }

    public function modifyQueryUsing(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    public function modifyBaseQueryUsing(?Closure $callback): static
    {
        $this->modifyBaseQueryUsing = $callback;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label) ?? (string) str($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getSummary(): string
    {
        return $this->evaluate($this->summary);
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query;
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        if ($this->hasBaseQueryModificationCallback()) {
            return $this->evaluate($this->modifyBaseQueryUsing, [
                'query' => $query,
            ]) ?? $query;
        }

        if ($this->queriesRelationshipsUsingSubSelect()) {
            return $query->whereHas(
                $this->getConstraint()->getRelationshipName(),
                function (Builder $query): Builder {
                    $modifyRelationshipQueryUsing = $this->getConstraint()->getModifyRelationshipQueryUsing();

                    if ($modifyRelationshipQueryUsing) {
                        $query = $this->evaluate($modifyRelationshipQueryUsing, [
                            'query' => $query,
                        ]) ?? $query;
                    }

                    $qualifiedColumn = $query->qualifyColumn($this->getConstraint()->getAttributeForQuery());

                    if ($this->hasQueryModificationCallback()) {
                        return $this->evaluate($this->modifyQueryUsing, [
                            'column' => $qualifiedColumn,
                            'qualifiedColumn' => $qualifiedColumn,
                            'query' => $query,
                        ]) ?? $query;
                    }

                    return $this->apply($query, $qualifiedColumn);
                },
            );
        }

        $qualifiedColumn = $query->qualifyColumn($this->getConstraint()->getAttributeForQuery());

        if ($this->hasQueryModificationCallback()) {
            return $this->evaluate($this->modifyQueryUsing, [
                'column' => $qualifiedColumn,
                'qualifiedColumn' => $qualifiedColumn,
                'query' => $query,
            ]) ?? $query;
        }

        return $this->apply($query, $qualifiedColumn);
    }

    public function applyToBaseFilterQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public function getFormSchema(): array
    {
        return [];
    }

    public function constraint(?Constraint $constraint): static
    {
        $this->constraint = $constraint;

        return $this;
    }

    /**
     * @param  array<string, mixed> | null  $settings
     */
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

    /**
     * @return array<string, mixed> | null
     */
    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function isInverse(): ?bool
    {
        return $this->isInverse;
    }

    public function queriesRelationshipsUsingSubSelect(): bool
    {
        return $this->getConstraint()->queriesRelationships();
    }

    protected function hasQueryModificationCallback(): bool
    {
        return $this->modifyQueryUsing instanceof Closure;
    }

    protected function hasBaseQueryModificationCallback(): bool
    {
        return $this->modifyBaseQueryUsing instanceof Closure;
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'constraint' => [$this->getConstraint()],
            'isInverse' => [$this->isInverse()],
            'settings' => [$this->getSettings()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
