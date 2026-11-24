<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->caseInsensitiveText('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('website')->nullable();
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postalcode')->nullable();
            $table->string('country')->nullable();
            $table->jsonb('domains')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->uniqueIndex('name', 'organizations_name_unique')->where(fn (Builder $query) => $query->whereNull('deleted_at'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
