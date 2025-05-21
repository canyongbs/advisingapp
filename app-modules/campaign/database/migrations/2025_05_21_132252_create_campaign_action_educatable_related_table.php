<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaign_action_educatable_related', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('campaign_action_educatable_id')->constrained('campaign_action_educatable')->cascadeOnDelete();
            $table->string('related_id');
            $table->string('related_type');

            $table->timestamps();

            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_action_educatable_related');
    }
};
