<?php

use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    // TODO: Cleanup Task DepartmentCitextCleanup - remove FixesDuplicateNames trait & usages
    use FixesDuplicateNames;

    protected string $table = 'teams';

    protected string $column = 'name';

    /** @var array<int, string> */
    protected array $groupByColumns = [];

    // TODO: Cleanup Task DepartmentCitextCleanup - remove $chunkSize and $usesSoftDeletes
    protected int $chunkSize = 500;

    protected bool $usesSoftDeletes = true;

    private string $uniqueConstraint = 'teams_name_unique';

    public function up(): void
    {
        DB::transaction(function () {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropUnique($this->uniqueConstraint);
            });

            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table) {
                $table->uniqueIndex([...$this->groupByColumns, $this->column], $this->uniqueConstraint)
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropUniqueIndex($this->uniqueConstraint);
                
                $table->unique([...$this->groupByColumns, $this->column], $this->uniqueConstraint);
            });

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");
        });
    }
};
