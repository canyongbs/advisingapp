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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
     * Deduplicates a table by renaming case-insensitively duplicate names per owner.
     *
     * @param  string  $table  The table name to deduplicate
     * @param  string  $ownerColumn  The column that identifies the owner/parent (e.g., 'advisor_id')
     */
    protected function fixCaseInsensitiveDuplicateNames(string $table, string $ownerColumn): void
    {
        /** @var array<string, array<string, bool>> $seenByOwner */
        $seenByOwner = [];
        /** @var array<string, string> $updates */
        $updates = [];

        $this->orderDuplicateRecords($table, $ownerColumn)
            ->chunk($this->chunkSize, function (Collection $records) use ($table, $ownerColumn, &$seenByOwner, &$updates) {
                foreach ($records as $record) {
                    $ownerId = (string) $record->{$ownerColumn};
                    $normalizedName = Str::lower((string) $record->name);

                    if (! array_key_exists($ownerId, $seenByOwner)) {
                        $seenByOwner[$ownerId] = [];
                    }

                    if (! isset($seenByOwner[$ownerId][$normalizedName])) {
                        $seenByOwner[$ownerId][$normalizedName] = true;

                        continue;
                    }

                    $uniqueName = $this->buildDeduplicatedValue(
                        table: $table,
                        ownerColumn: $ownerColumn,
                        ownerId: $ownerId,
                        originalName: (string) $record->name,
                        recordId: (string) $record->id,
                    );

                    $updates[$record->id] = $uniqueName;
                    $seenByOwner[$ownerId][Str::lower($uniqueName)] = true;

                    if (count($updates) >= $this->chunkSize) {
                        DB::transaction(function () use ($table, $updates) {
                            $this->batchUpdate($table, $updates);
                        });
                        $updates = [];
                    }
                }
            });

        // Process any remaining updates
        if (count($updates) > 0) {
            DB::transaction(function () use ($table, $updates) {
                $this->batchUpdate($table, $updates);
            });
        }
    }

    /**
     * Executes a batch update using a CASE statement for efficiency.
     *
     * @param  string  $table  The table name
     * @param  array<string, string>  $updates  Map of record ID => new name
     */
    protected function batchUpdate(string $table, array $updates): void
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

        $sql = "UPDATE {$table} SET name = CASE " . implode(' ', $cases) . " END, updated_at = NOW() WHERE id IN ({$idPlaceholders})";

        DB::statement($sql, $bindings);
    }

    /**
     * Orders duplicate records for processing.
     * Ensures live records are processed first, soft-deleted records second.
     */
    protected function orderDuplicateRecords(string $table, string $ownerColumn): Builder
    {
        $query = DB::table($table)
            ->select(['id', $ownerColumn, 'name'])
            ->orderBy($ownerColumn)
            ->orderByRaw('LOWER(name)');

        // Exclude soft-deleted records if usesSoftDeletes is true
        if ($this->usesSoftDeletes) { // @phpstan-ignore property.notFound
            $query->whereNull('deleted_at');
        }

        return $query->orderBy('id');
    }

    /**
     * Checks if a table has a deleted_at column.
     */
    protected function tableHasDeletedAt(string $table): bool
    {
        return DB::getSchemaBuilder()->hasColumn($table, 'deleted_at');
    }

    /**
     * Builds a unique deduplicatedValue with a numeric suffix.
     * Truncates the original name to fit the suffix within the max length.
     */
    protected function buildDeduplicatedValue(
        string $table,
        string $ownerColumn,
        string $ownerId,
        string $originalName,
        string $recordId,
    ): string {
        $baseName = trim($originalName);

        if ($baseName === '') {
            $baseName = 'Category';
        }

        $suffixCounter = 2;

        while (true) {
            $suffix = ' (' . $suffixCounter . ')';
            $maxLength = 255 - strlen($suffix);
            $candidateBase = Str::substr($baseName, 0, max(1, $maxLength));
            $candidate = $candidateBase . $suffix;

            $exists = DB::table($table)
                ->where($ownerColumn, $ownerId)
                ->where('id', '<>', $recordId)
                ->whereRaw('LOWER(name) = ?', [Str::lower($candidate)])
                ->exists();

            if (! $exists) {
                return $candidate;
            }

            $suffixCounter++;
        }
    }
}
