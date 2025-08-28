<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('workflow_engagement_sms_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workflow_step_id');
        });
    }

    public function down(): void
    {
        Schema::table('workflow_engagement_sms_details', function (Blueprint $table) {
            $table->foreignUuid('workflow_step_id')->nullable()->constrained('workflow_steps');
        });
    }
};
