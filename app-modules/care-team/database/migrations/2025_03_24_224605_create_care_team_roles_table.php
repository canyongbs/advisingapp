<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_team_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('type');
            $table->boolean('is_default');

            $table->uniqueIndex(['type', 'is_default'])->where(fn (Builder $condition) => $condition->whereNull('deleted_at')->where('is_default', true));

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_team_roles');
    }
};
