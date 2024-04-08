<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropForeign(['interaction_campaign_id']);
            $table->dropColumn('interaction_campaign_id');
        });

        Schema::dropIfExists('interaction_campaigns');
    }

    public function down(): void
    {
        Schema::create('interaction_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('interactions', function (Blueprint $table) {
            $table->foreignUuid('interaction_campaign_id')->nullable()->constrained('interaction_campaigns');
        });
    }
};
