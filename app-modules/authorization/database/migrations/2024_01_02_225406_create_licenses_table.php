<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();

            $table->uniqueIndex(['user_id', 'type'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
        });
    }
};
