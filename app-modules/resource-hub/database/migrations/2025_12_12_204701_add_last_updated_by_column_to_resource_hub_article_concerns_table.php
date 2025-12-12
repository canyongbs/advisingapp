<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('resource_hub_article_concerns', function (Blueprint $table) {
            $table->foreignUuid('last_updated_by_id')->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('resource_hub_article_concerns', function (Blueprint $table) {
            $table->dropColumn('last_updated_by_id');
        });
    }
};
