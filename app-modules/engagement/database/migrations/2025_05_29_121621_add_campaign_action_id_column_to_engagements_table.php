<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->foreignUuid('campaign_action_id')->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campaign_action_id');
        });
    }
};
