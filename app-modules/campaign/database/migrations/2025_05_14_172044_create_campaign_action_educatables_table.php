<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaign_action_educatables', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('campaign_action_id')->constrained('campaign_actions')->cascadeOnDelete();
            $table->string('educatable_type');
            $table->string('educatable_id');
            $table->timestampTz('succeeded_at')->nullable();
            $table->timestampTz('last_failed_at')->nullable();
            $table->nullableUuidMorphs('related');

            $table->timestampsTz();

            $table->uniqueIndex(['educatable_type', 'educatable_id', 'campaign_action_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_action_educatables');
    }
};
