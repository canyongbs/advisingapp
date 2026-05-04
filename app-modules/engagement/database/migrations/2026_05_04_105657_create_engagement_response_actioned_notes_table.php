<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('engagement_response_actioned_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('engagement_response_id')->constrained('engagement_responses')->onDelete('cascade');
            $table->foreignUuid('created_by_id')->constrained('users')->nullOnDelete();
            $table->foreignUuid('last_updated_by_id')->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_response_actioned_notes');
    }
};
