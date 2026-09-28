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

namespace Database\Migrations\Concerns;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** @phpstan-ignore trait.unused */
trait FixesDuplicateNames
{
    /**
     * Order duplicate records so the first one is kept and the rest are renamed.
     * On soft-deleting tables live records are preferred over soft-deleted ones,
     * then the oldest record is kept.
     */
    protected function orderDuplicateRecords(Builder $query): Builder
    {
        if ($this->usesSoftDeletes) {
            $query->orderByRaw("{$this->softDeleteColumn()} IS NULL DESC");
        }

        return $query
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');
    }

    protected function ignoresNullValues(): bool
    {
        return false;
    }

    /**
     * The column used to exclude "removed" records from de-duplication.
     *
     * Defaults to `deleted_at` (Laravel soft deletes). Override to `archived_at`
     * for models that use archiving instead of soft deletes.
     */
    protected function softDeleteColumn(): string
    {
        return 'deleted_at';
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function existingValueMatchPatterns(string $baseValue): array
    {
        $lower = Str::lower($baseValue);

        return [$lower, $lower . '-%'];
    }

    protected function buildDeduplicatedValue(string $originalValue, int $counter): string
    {
        $suffix = "-{$counter}";
        $maxLength = 255 - strlen($suffix);

        return Str::substr($originalValue, 0, max(1, $maxLength)) . $suffix;
    }

    protected function deduplicatedValuePattern(): string
    {
        return '-[0-9]+$';
    }

    protected function stripDeduplicatedSuffix(string $value): string
    {
        return preg_replace('/-\d+$/', '', $value) ?? $value;
    }

    private function fixDuplicates(): void
    {
        /** @var array<int, string> $groupByColumns */
        $groupByColumns = $this->groupByColumns ?? []; // @phpstan-ignore property.notFound, nullCoalesce.property

        $query = DB::table($this->table)
            ->select([
                ...$groupByColumns,
                DB::raw("LOWER({$this->column}) as lower_name"),
            ]);

        if ($this->usesSoftDeletes) {
            $query->whereNull($this->softDeleteColumn());
        }

        if ($this->ignoresNullValues()) {
            $query->whereNotNull($this->column);
        }

        $duplicates = $query
            ->groupBy([
                ...$groupByColumns,
                DB::raw("LOWER({$this->column})"),
            ])
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        foreach ($duplicates as $duplicate) {
            $this->processDuplicateGroup($duplicate->lower_name, (array) $duplicate, $groupByColumns);
        }
    }

    /**
     * @param  array<string, mixed>  $groupValues
     * @param  array<int, string>  $groupByColumns
     */
    private function processDuplicateGroup(string $duplicateName, array $groupValues = [], array $groupByColumns = []): void
    {
        $recordsQuery = DB::table($this->table)
            ->select('id', $this->column, 'created_at')
            ->whereRaw("LOWER({$this->column}) = ?", [$duplicateName]);

        foreach ($groupByColumns as $col) {
            $recordsQuery->where($col, $groupValues[$col]);
        }

        if ($this->usesSoftDeletes) {
            $recordsQuery->whereNull($this->softDeleteColumn());
        }

        $records = $this->orderDuplicateRecords($recordsQuery)->get();

        if ($records->count() <= 1) {
            return;
        }

        /** @var string $baseName */
        $baseName = $records->first()->{$this->column};

        $existingNamesQuery = DB::table($this->table);

        if ($this->usesSoftDeletes) {
            $existingNamesQuery->whereNull($this->softDeleteColumn());
        }

        foreach ($groupByColumns as $col) {
            $existingNamesQuery->where($col, $groupValues[$col]);
        }

        [$existingExact, $existingLike] = $this->existingValueMatchPatterns($baseName);

        /** @var array<string, int> $existingNames */
        $existingNames = $existingNamesQuery
            ->where(function (Builder $query) use ($existingExact, $existingLike) {
                $query->whereRaw("LOWER({$this->column}) = ?", [$existingExact])
                    ->orWhereRaw("LOWER({$this->column}) LIKE ?", [$existingLike]);
            })
            ->pluck($this->column)
            /** @phpstan-ignore argument.type */
            ->map(fn (mixed $name): string => Str::lower(strval($name)))
            ->flip()
            ->all();

        $updates = [];
        $counter = 2;

        foreach ($records->skip(1) as $record) {
            /** @var string $originalName */
            $originalName = $record->{$this->column};
            $newName = $this->buildDeduplicatedValue($originalName, $counter);

            while (isset($existingNames[Str::lower($newName)])) {
                $counter++;
                $newName = $this->buildDeduplicatedValue($originalName, $counter);
            }

            $updates[$record->id] = $newName;
            $existingNames[Str::lower($newName)] = true;
            $counter++;

            if (count($updates) >= $this->chunkSize) {
                $this->batchUpdate($updates);
                $updates = [];
            }
        }

        if (! empty($updates)) {
            $this->batchUpdate($updates);
        }
    }

    /**
     * @param  array<string, string>  $updates
     */
    private function batchUpdate(array $updates): void
    {
        if (empty($updates)) {
            return;
        }

        $cases = [];
        $ids = [];
        $bindings = [];

        foreach ($updates as $id => $newName) {
            $cases[] = 'WHEN id = ? THEN ?';
            $bindings[] = $id;
            $bindings[] = $newName;
            $ids[] = $id;
        }

        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        $bindings = array_merge($bindings, $ids);

        $sql = "UPDATE {$this->table} SET {$this->column} = CASE " . implode(' ', $cases) . " END WHERE id IN ({$idPlaceholders})";

        DB::statement($sql, $bindings);
    }

    private function revertDuplicates(): void
    {
        /** @var array<int, string> $groupByColumns */
        $groupByColumns = $this->groupByColumns ?? []; // @phpstan-ignore property.notFound, nullCoalesce.property

        $query = DB::table($this->table)
            ->select(['id', $this->column, ...$groupByColumns])
            ->whereRaw("{$this->column} ~ ?", [$this->deduplicatedValuePattern()]);

        if ($this->usesSoftDeletes) {
            $query->whereNull($this->softDeleteColumn());
        }

        $query
            ->orderBy('id')
            ->chunk($this->chunkSize, function (Collection $records) use ($groupByColumns): void {
                $updates = [];

                foreach ($records as $record) {
                    /** @var string $currentName */
                    $currentName = $record->{$this->column};
                    /** @var string $originalName */
                    $originalName = $this->stripDeduplicatedSuffix($currentName);

                    $conflictQuery = DB::table($this->table)
                        ->whereRaw("LOWER({$this->column}) = ?", [Str::lower(strval($originalName))]);

                    foreach ($groupByColumns as $col) {
                        $conflictQuery->where($col, $record->{$col});
                    }

                    if ($this->usesSoftDeletes) {
                        $conflictQuery->whereNull($this->softDeleteColumn());
                    }

                    if (! $conflictQuery->where('id', '!=', $record->id)->exists()) {
                        $updates[$record->id] = $originalName;
                    }
                }

                if (! empty($updates)) {
                    DB::transaction(function () use ($updates) {
                        $this->batchUpdate($updates);
                    });
                }
            });
    }
}
