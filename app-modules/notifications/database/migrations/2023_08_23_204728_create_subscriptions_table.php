<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('subscribable_id');
            $table->string('subscribable_type');
            $table->timestamps();
            $table->softDeletes();

            $table->uniqueIndex(['user_id', 'subscribable_id', 'subscribable_type'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at'));
        });
    }
};
