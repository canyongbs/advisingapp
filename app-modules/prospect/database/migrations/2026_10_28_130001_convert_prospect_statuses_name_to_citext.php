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

use App\Features\ProspectSourceAndStatusNameCitextFeature;
use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    // TODO: Cleanup Task (prospect-source-status-citext) - remove FixesDuplicateNames trait & usages (if no other migration uses it, restore its trait.unused ignore annotation)
    use FixesDuplicateNames;

    protected string $table = 'prospect_statuses';

    protected string $column = 'name';

    // TODO: Cleanup Task (prospect-source-status-citext) - remove $chunkSize and $usesSoftDeletes
    protected int $chunkSize = 500;

    protected bool $usesSoftDeletes = true;

    private string $uniqueConstraint = 'prospect_statuses_name_unique';

    private string $lengthConstraint = 'prospect_statuses_name_length';

    public function up(): void
    {
        DB::transaction(function (): void {
            // TODO: Cleanup Task (prospect-source-status-citext) - remove the trigger toggle and the $this->fixDuplicates() call (the surrounding schema changes are permanent)

            DB::statement("ALTER TABLE {$this->table} DISABLE TRIGGER prevent_modification_of_system_protected_rows");

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ENABLE TRIGGER prevent_modification_of_system_protected_rows");

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            // citext has no length modifier, so preserve the original varchar(255) limit with a
            // CHECK constraint. Without it, names longer than 255 chars could be stored here but
            // would be rejected by the prospect create/update APIs' max:255 rule.
            DB::statement("ALTER TABLE {$this->table} ADD CONSTRAINT {$this->lengthConstraint} CHECK (char_length({$this->column}) <= 255)");

            Schema::table($this->table, function (Blueprint $table): void {
                $table->uniqueIndex([$this->column], $this->uniqueConstraint)
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });

            ProspectSourceAndStatusNameCitextFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ProspectSourceAndStatusNameCitextFeature::deactivate();

            Schema::table($this->table, function (Blueprint $table): void {
                $table->dropUniqueIndex($this->uniqueConstraint);
            });

            DB::statement("ALTER TABLE {$this->table} DROP CONSTRAINT {$this->lengthConstraint}");

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");
        });
    }

    /**
     * Prefer retaining system-protected statuses (e.g. the default "New" and
     * "Converted" rows) so de-duplication never renames them. Those rows are
     * looked up by name + is_system_protected by ConvertToStudent and
     * DisassociateStudent, which would break if they were suffixed.
     */
    // TODO: Cleanup Task (prospect-source-status-citext) - remove this duplicate ordering override
    protected function orderDuplicateRecords(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_system_protected')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');
    }
};
