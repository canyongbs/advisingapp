<?php

namespace App\Filament\Filters;

use App\Models\Scopes\Archived;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class ArchivedFilter extends TernaryFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Archived records')
            ->trueLabel('With archived records')
            ->falseLabel('Only archived records')
            ->placeholder('Without archived records')
            ->queries(
                true: fn (Builder $query) => $query,
                false: fn (Builder $query) => $query->tap(new Archived()),
                blank: fn (Builder $query) => $query->whereNot->tap(new Archived()),
            )
            ->indicateUsing(function (array $state): array {
                if ($state['value'] ?? null) {
                    return [Indicator::make($this->getTrueLabel())];
                }

                if (blank($state['value'] ?? null)) {
                    return [];
                }

                return [Indicator::make($this->getFalseLabel())];
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'archived';
    }
}
