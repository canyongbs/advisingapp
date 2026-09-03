<?php

use Database\Migrations\Concerns\FixesDuplicateNames;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    // TODO: Cleanup Task PromptCiTextCleanup - remove FixesDuplicateNames trait & usages (if no other migration uses it, restore its trait.unused ignore annotation)
    use FixesDuplicateNames;

    protected string $table = 'prompt_types';

    protected string $column = 'title';

    // TODO: Cleanup Task PromptCiTextCleanup - remove $chunkSize and $usesSoftDeletes
    protected int $chunkSize = 500;

    protected bool $usesSoftDeletes = true;

    private string $uniqueConstraint = 'prompt_types_title_unique';

    public function up(): void
    {
        DB::transaction(function () {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropUniqueIndex($this->uniqueConstraint);
            });

            // TODO: Cleanup Task PromptCiTextCleanup - remove the $this->fixDuplicates() call (the surrounding schema changes are permanent)
            $this->fixDuplicates();

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE citext");

            Schema::table($this->table, function (Blueprint $table) {
                $table->uniqueIndex([$this->column], $this->uniqueConstraint)
                    ->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropUniqueIndex($this->uniqueConstraint);

                $table->unique([$this->column], $this->uniqueConstraint);
            });

            DB::statement("ALTER TABLE {$this->table} ALTER COLUMN {$this->column} TYPE varchar(255)");
        });
    }
};
