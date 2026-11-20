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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Indexes the `sisid` of enrollments that are not soft deleted, so a student's enrollments can be found, counted and checked for from the index alone.
 *
 * A student data import copies `enrollments` into a temporary `import_*_enrollments` table, with its indexes, and swaps it in when it completes.
 * Swapped in tables keep the names their indexes were given in the copy, so indexes are found by their definition rather than their name.
 */
return new class () extends Migration {
    private const string INDEX_DEFINITION = 'USING btree (sisid) WHERE (deleted_at IS NULL)';

    private const string UNDEFINED_TABLE_SQLSTATE = '42P01';

    // CREATE INDEX CONCURRENTLY cannot run inside a transaction block.
    public $withinTransaction = false;

    // Each index is built concurrently, which cannot run inside a transaction, and is skipped if it already exists, so a retry picks up where a failure left off.
    // @phpstan-ignore Common.multipleMigrationChangesNotWrappedInTransaction
    public function up(): void
    {
        // Indexed first, so imports that start meanwhile copy the index.
        $this->createIndex('enrollments');

        foreach ($this->getImportEnrollmentsTables() as $table) {
            $this->createIndex($table);
        }

        // An import that completed meanwhile swapped in a table that may not have the index yet.
        $this->createIndex('enrollments');
    }

    public function down(): void
    {
        foreach (['enrollments', ...$this->getImportEnrollmentsTables()] as $table) {
            foreach ($this->getIndexes($table) as $index) {
                DB::statement("DROP INDEX CONCURRENTLY IF EXISTS \"{$index}\"");
            }
        }
    }

    private function createIndex(string $table): void
    {
        // A failed concurrent build leaves an invalid index behind, so a retry must drop it before building again.
        foreach ($this->getIndexes($table, isValid: false) as $index) {
            DB::statement("DROP INDEX CONCURRENTLY IF EXISTS \"{$index}\"");
        }

        if (filled($this->getIndexes($table, isValid: true))) {
            return;
        }

        // Built concurrently so enrollments can still be written, and imports completed, while the index builds.
        // A name is only given on `enrollments`, as the generated names of the import tables' indexes could exceed Postgres' limit.
        $name = ($table === 'enrollments') ? 'enrollments_active_sisid_index' : '';

        try {
            DB::statement("CREATE INDEX CONCURRENTLY {$name} ON \"{$table}\" (sisid) WHERE deleted_at IS NULL");
        } catch (QueryException $exception) {
            // An import that completed meanwhile renamed its table to `enrollments`, which is indexed again afterwards.
            if (($table === 'enrollments') || ($exception->getCode() !== self::UNDEFINED_TABLE_SQLSTATE)) {
                throw $exception;
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getIndexes(string $table, ?bool $isValid = null): array
    {
        return DB::table('pg_index')
            ->join('pg_class', 'pg_class.oid', '=', 'pg_index.indexrelid')
            ->whereRaw('pg_index.indrelid = to_regclass(?)', ["\"{$table}\""])
            ->whereRaw('pg_get_indexdef(pg_index.indexrelid) LIKE ?', ['%' . self::INDEX_DEFINITION])
            ->when($isValid !== null, fn (Builder $query): Builder => $query->where('pg_index.indisvalid', $isValid))
            ->pluck('pg_class.relname')
            ->all();
    }

    /**
     * The temporary enrollments tables of student data imports that have not completed.
     *
     * @return array<string>
     */
    private function getImportEnrollmentsTables(): array
    {
        return DB::table('pg_tables')
            ->whereRaw('schemaname = current_schema()')
            ->where('tablename', 'like', 'import\_%\_enrollments')
            ->pluck('tablename')
            ->all();
    }
};
