<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasGlobalSearchResultScoring
{
    protected static function scoreGlobalSearchResults(
        Builder $query,
        string $search,
        array $attributeScores,
    ): void {
        $cases = [];

        foreach ($attributeScores as $attribute => $maxScore) {
            $cases[] = [$attribute, $search, $maxScore];
            $cases[] = [$attribute, "{$search}%", $maxScore * (2 / 3)];
            $cases[] = [$attribute, "%{$search}%", $maxScore / 3];
        }

        usort($cases, fn (array $a, array $b) => $a[2] <=> $b[2]);

        [$cases, $bindings] = array_reduce(
            $cases,
            function (array $carry, array $case): array {
                [$attribute, $binding, $score] = $case;

                $carry[0][] = "lower({$attribute}::text)::text like ? then {$score}";
                $carry[1][] = $binding;

                return $carry;
            },
            [[], []],
        );

        $query->orderByRaw(
            'case when ' . implode(' when ', $cases) . ' else 0 end desc',
            $bindings,
        );
    }
}
