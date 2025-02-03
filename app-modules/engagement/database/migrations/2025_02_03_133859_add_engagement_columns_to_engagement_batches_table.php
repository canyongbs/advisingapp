<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engagement_batches', function (Blueprint $table) {
            $table->string('channel')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->string('subject')->nullable();
            $table->jsonb('body')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->unsignedInteger('total_engagements')->nullable();
            $table->unsignedInteger('processed_engagements')->nullable();
            $table->unsignedInteger('successful_engagements')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('engagement_batches', function (Blueprint $table) {
            $table->dropColumn('channel');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('subject');
            $table->dropColumn('body');
            $table->dropColumn('scheduled_at');
            $table->dropColumn('total_engagements');
            $table->dropColumn('processed_engagements');
            $table->dropColumn('successful_engagements');
        });
    }
};
