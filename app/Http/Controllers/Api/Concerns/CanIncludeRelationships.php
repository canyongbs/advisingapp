<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;

trait CanIncludeRelationships
{
    /**
     * @param array<string, string> $relationships
     *
     * @return array<string>
     */
    protected function getIncludedRelationshipsToLoad(Request $request, array $relationships): array
    {
        $relationships = collect($relationships)
            ->mapWithKeys(fn (string $value, string $key): array => is_numeric($key) ? [$value => $value] : [$key => $value])
            ->all();

        $allowedIncludes = array_keys($relationships);

        $include = $request->query('include', '');
        $includes = array_filter(
            explode(',', $include),
            filled(...),
        );

        if (! (config('query-builder.disable_invalid_includes_query_exception') ?? false)) {
            $invalidIncludesCollection = collect($includes)->diff($allowedIncludes);

            if ($invalidIncludesCollection->count()) {
                throw InvalidIncludeQuery::includesNotAllowed($invalidIncludesCollection, collect($allowedIncludes));
            }
        }

        return collect($includes)
            ->map(fn (string $relationship): string => $relationships[$relationship])
            ->all();
    }
}
