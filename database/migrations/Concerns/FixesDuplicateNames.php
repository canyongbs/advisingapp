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

trait FixesDuplicateNames
{
    /**
     * Deduplicates `$this->table` by renaming case-insensitively duplicate
     * `$this->column` values. When `$this->groupByColumns` is set, uniqueness is
     * scoped per group (e.g. an owner/parent column); otherwise it is table-wide.
     */
    protected function fixCaseInsensitiveDuplicateNames(): void
    {
        /** @var string $table */
        $table = $this->table; // @phpstan-ignore property.notFound
        /** @var string $column */
        $column = $this->column; // @phpstan-ignore property.notFound
        /** @var array<int, string> $groupByColumns */
        $groupByColumns = $this->groupByColumns ?? []; // @phpstan-ignore property.notFound, nullCoalesce.property

        /** @var array<string, array<string, bool>> $seenByGroup */
        $seenByGroup = [];
        /** @var array<string, string> $updates */
        $updates = [];

        $this->orderDuplicateRecords($table, $column, $groupByColumns)
            ->chunk($this->chunkSize, function (Collection $records) use ($table, $column, $groupByColumns, &$seenByGroup, &$updates) {
                foreach ($records as $record) {
                    $groupKey = $this->buildGroupKey($record, $groupByColumns);
                    $normalizedName = Str::lower((string) $record->{$column});

                    if (! isset($seenByGroup[$groupKey][$normalizedName])) {
                        $seenByGroup[$groupKey][$normalizedName] = true;

                        continue;
                    }

                    $uniqueName = $this->buildDeduplicatedValue(
                        table: $table,
                        column: $column,
                        groupByColumns: $groupByColumns,
                        record: $record,
                    );

                    $updates[$record->id] = $uniqueName;
                    $seenByGroup[$groupKey][Str::lower($uniqueName)] = true;

                    if (count($updates) >= $this->chunkSize) {
                        DB::transaction(function () use ($table, $column, $updates) {
                            $this->batchUpdate($table, $column, $updates);
                        });
                        $updates = [];
                    }
                }
            });

        if (count($updates) > 0) {
            DB::transaction(function () use ($table, $column, $updates) {
                $this->batchUpdate($table, $column, $updates);
            });
        }
    }

    /**
     *
     * @param  array<string, string>  $updates  Map of record ID => new value
     */
    protected function batchUpdate(string $table, string $column, array $updates): void
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

        $sql = "UPDATE {$table} SET {$column} = CASE " . implode(' ', $cases) . " END, updated_at = NOW() WHERE id IN ({$idPlaceholders})";

        DB::statement($sql, $bindings);
    }

    /**
     *
     * @param  array<int, string>  $groupByColumns
     */
    protected function orderDuplicateRecords(string $table, string $column, array $groupByColumns): Builder
    {
        $query = DB::table($table)
            ->select(['id', $column, ...$groupByColumns]);

        foreach ($groupByColumns as $groupByColumn) {
            $query->orderBy($groupByColumn);
        }

        $query->orderByRaw("LOWER({$column})");

        // Exclude soft-deleted records if usesSoftDeletes is true
        if ($this->usesSoftDeletes) { // @phpstan-ignore property.notFound
            $query->whereNull('deleted_at');
        }

        return $query->orderBy('id');
    }

    /**
     *
     * @param  array<int, string>  $groupByColumns
     */
    protected function buildGroupKey(object $record, array $groupByColumns): string
    {
        if (empty($groupByColumns)) {
            return '';
        }

        return implode('|', array_map(
            fn (string $groupByColumn): string => (string) $record->{$groupByColumn},
            $groupByColumns,
        ));
    }

    /**
     *
     * @param  array<int, string>  $groupByColumns
     */
    protected function buildDeduplicatedValue(
        string $table,
        string $column,
        array $groupByColumns,
        object $record,
    ): string {
        $baseName = trim((string) $record->{$column});

        if ($baseName === '') {
            $baseName = 'Category';
        }

        $suffixCounter = 2;

        while (true) {
            $suffix = ' (' . $suffixCounter . ')';
            $maxLength = 255 - strlen($suffix);
            $candidateBase = Str::substr($baseName, 0, max(1, $maxLength));
            $candidate = $candidateBase . $suffix;

            $query = DB::table($table)
                ->where('id', '<>', $record->id)
                ->whereRaw("LOWER({$column}) = ?", [Str::lower($candidate)]);

            foreach ($groupByColumns as $groupByColumn) {
                $query->where($groupByColumn, $record->{$groupByColumn});
            }

            if (! $query->exists()) {
                return $candidate;
            }

            $suffixCounter++;
        }
    }
}
